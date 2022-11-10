<?php
/**
 * User: fabio
 * Date: 11.05.19
 * Time: 21:28
 */

namespace App\Classes;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\FeedValidatorException;
use App\Exceptions\FeedValidatorWarning;
use App\Exceptions\LogoMissingException;
use App\Models\Feed;

abstract class FeedValidator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $feed;
    protected $username;

    /**
     * @var null
     */
    protected $uuid;

    /**
     * FeedValidator constructor.
     * @param  string  $username
     * @param  string  $feedId
     * @param  string|null  $uuid
     */
    public function __construct(string $username, string $feedId, string $uuid = null)
    {
        $this->username = $username;
        $this->feed = Feed::whereUsername($username)->findOrFail($feedId);
        $this->uuid = $uuid;
    }

    public function run(): array
    {
        $errors = [];
        $warnings = [];

        foreach($this->getCheckMethods() as $checkMethod) {
            try {
                call_user_func([$this, $checkMethod]);
            } catch (FeedValidatorWarning $e) {
                array_push($warnings, $e->getMessage());
            } catch (LogoMissingException $e) {
                array_push($errors, $e->getMessage());
                break;
            } catch (FeedValidatorException $e) {
                array_push($errors, $e->getMessage());
            } catch (\Exception $e) {
                array_push($errors, $e->getMessage());
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * @param  mixed  ...$args
     * @throws FeedValidatorException
     * @throws FeedValidatorWarning
     * @throws \ReflectionException
     */
    protected function fire(...$args)
    {
        $callingMethod = array_shift($args);

        if ($args[0] != 'success') {
            // Translate error messages with replacements
            $args[1] = trans('feeds.' . $args[1], $args[2] ?? []);
        }

        if (!is_null($this->uuid)) {
            $pre = 'App\Events\FeedValidator' . (new \ReflectionClass($this))->getShortName();
            $class = $pre . ucfirst($callingMethod);
            array_unshift($args, $this->uuid);
            // Fire event
            event(new $class($args));

            if (in_array($args[1], ['warning', 'error', 'danger'])) {
                return false;
            }
        } else {
            if ($args[0] == 'warning') {
                throw new FeedValidatorWarning($args[1]);
            } elseif($args[0] == 'error') {
                if (in_array($callingMethod,['checkLogoIsSet', 'checkLogoExists'])) {
                    throw new LogoMissingException($args[1]);
                }
                throw new FeedValidatorException($args[1]);
            }
        }

        return true;
    }

    protected function writeFeed()
    {
/*        $feedWriter = new FeedWriterLegacy();
        $feedWriter->write($this->username, $this->feed->feed_id);*/
        refresh_feed($this->username, $this->feed->feed_id);
    }

    public function getCheckMethods()
    {
        $methods = collect(get_class_methods(get_class($this)));
        $aCheck = $methods->filter(function($value, $key) {
            return strpos($value, 'check') === 0;
        });

        return $aCheck;
    }

    /**
     * @return mixed|null
     */
    protected function getEntry()
    {
        return get_newest_show($this->feed);
    }
}
