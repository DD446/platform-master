<?php
/**
 * User: fabio
 * Date: 08.01.14
 * Time: 23:31
 */

namespace App\Classes;

use App\Models\Feed;
use Illuminate\Support\Str;
use App\Exceptions\ServerConfigWriteException;

class NginxServerConfigWriter {

    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;

    const SPEED_HD = '1024k';
    const SPEED_SD = '768k';

    const WEBSITETYPE_NONE = false;
    const WEBSITETYPE_WORDPRESS = 'wordpress';
    const WEBSITETYPE_TWIG = 'twig';
    const WEBSITETYPE_TWIG_CUSTOM = 'twig_custom';

    private $aConfig = [];
    private $logDir  = '/var/log/nginx/hosting';
    private $baseDir = '/var/www/www.podcaster.de/portal';
    private $newBaseDir = '/var/www/podcaster.de/current';
    private $confDir = '/lib/data/hostingstorage/conf';
    private $websiteType = self::WEBSITETYPE_NONE;
    private $redirectUrlBlog;
    private $redirectUrlFeed = [];
    private $usesProtection = false;

    /**
     * NginxServerConfigWriter constructor.
     * @param  string  $user
     * @param  string  $serverName
     * @param  int|string  $port
     * @throws ServerConfigWriteException
     */
    public function __construct(string $user, string $serverName, string $port = self::PORT_HTTP)
    {
        if (!$serverName || $serverName == '.' || strlen($serverName) < 3) {
            throw new ServerConfigWriteException("ERROR: Server name cannot be empty");
        }

        $this->user = $user;
        $this->server_name = $serverName;
        $this->port = $port;
    }

    public function __set($name, $value)
    {
        $this->aConfig[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->aConfig)) {
            return $this->aConfig[$name];
        }

        return null;
    }

    public function __toString()
    {
        $userPath = $this->getUserPath($this->user);
        $output = "server {" . PHP_EOL;
        $output .= "\tlisten $this->port;" . PHP_EOL;
        $output .= "\tserver_name $this->server_name;" . PHP_EOL;
        $output .= "\taccess_log {$this->logDir}{$userPath}/access.log main;" . PHP_EOL;

        if (!is_null($this->limit_rate)) {
            $output .= "\tlimit_rate_after 2m;" . PHP_EOL;
            $output .= "\tlimit_rate {$this->limit_rate};" . PHP_EOL;
        }
        #$output .= "\tset {$this->user} \"{$this->user}\";" . PHP_EOL . PHP_EOL;
        $output .= PHP_EOL . PHP_EOL;
        $output .= <<<END

        if (!-e {$this->logDir}{$userPath}) {
            return 301 $this->baseUrl;
        }

        location ~ ^/download/(.+)$ {
END;
        if ($this->usesProtection) {
            $output .= PHP_EOL;
            $output .= <<<END
            auth_basic "Zugriff gesperrt";
            auth_basic_user_file {$this->newBaseDir}/storage/hostingstorage/conf/{$this->server_name}.htpasswd;
END;
        }
        $output .= PHP_EOL;
        $output .= <<<END
            location ~* .(json|mp3|m4a|aac|mp4|jpeg|jpg|png|gif)$ {
                 if (\$request_method = 'GET') {
                    add_header 'Access-Control-Allow-Origin' '*';
                    add_header 'Access-Control-Allow-Credentials' 'true';
                    add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
                    add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                 }
            }

            root {$this->baseDir}/www/mediafiles{$userPath};
            try_files \$uri \$uri/ /index.php;
        }

        location ~ ^/assets/(.+)$ {
                root {$this->newBaseDir}/public;
        }

        location ~ ^/([^/]+)/(logos)/(.+) {
                location ~* .(jpeg|jpg|png|gif)$ {
                     if (\$request_method = 'GET') {
                        add_header 'Access-Control-Allow-Origin' '*';
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                     }
                }

                root {$this->baseDir}/www/mediafiles{$userPath};
        }

        location ~ ^/([^/]+)/(media|graph)/(.+) {
END;
        if ($this->usesProtection) {
            $output .= PHP_EOL;
            $output .= <<<END
            auth_basic "Zugriff gesperrt";
            auth_basic_user_file {$this->newBaseDir}/storage/hostingstorage/conf/{$this->server_name}.htpasswd;
END;
        }
            $output .= PHP_EOL;
            $output .= <<<END
            location ~* .(json|mp3|m4a|aac|mp4|jpeg|jpg|png|gif|svg|webp|weba|webm|ogg|ogv)$ {
                 if (\$request_method = 'GET') {
                    add_header 'Access-Control-Allow-Origin' '*';
                    add_header 'Access-Control-Allow-Credentials' 'true';
                    add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
                    add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                 }
            }

            root {$this->baseDir}/www/mediafiles{$userPath};
        }

END;

        // Has to be above the default entries to overwrite them - Nginx requirement
        if ($this->getWebsiteType() == Feed::WEBSITE_TYPE_REDIRECT
            && !empty($this->getRedirectUrlBlog())) {
            $output .= PHP_EOL;
            $output .= <<<END
        location / {
            return 301 {$this->getRedirectUrlBlog()};
        }
END;
        }

        foreach($this->getRedirectUrlFeed() as $feedId => $feedUrl) {
            $output .= PHP_EOL . "\tlocation ~ (?i)/" . strtolower($feedId) . "(.rss|.atom)$ {"
                . PHP_EOL . "\t\treturn 301 {$feedUrl};" . PHP_EOL . "\t}";
        }

        $output .= <<<END

        location ~ /.*(.rss|.atom)$ {
END;
        if ($this->usesProtection) {
            $output .= PHP_EOL;
            $output .= <<<END
            auth_basic "Zugriff gesperrt";
            auth_basic_user_file {$this->newBaseDir}/storage/hostingstorage/conf/{$this->server_name}.htpasswd;
END;
            $output .= PHP_EOL;
        }
        $output .= <<<END
            root {$this->baseDir}/www/feedstorage{$userPath};
        }
END;
        $output .= PHP_EOL;


        $output .= PHP_EOL;
        $output .= <<<END
        location /crossdomain.xml {
                root   {$this->baseDir}/www;
        }
END;

        // If wordpress snippet is included for redirected blog url
        // there is a conflict with the "location /" directive!
        if ($this->getWebsiteType() != Feed::WEBSITE_TYPE_REDIRECT
            || empty($this->getRedirectUrlBlog())) {
        //if ($this->getWebsiteType() == self::WEBSITETYPE_WORDPRESS) {
            $output .= PHP_EOL;
            $output .= <<<END
        include include/wordpress;
END;
        }

            $output .= PHP_EOL;
            $output .= <<<END
        include include/php-fpm-wordpress;
END;

        if ($this->getWebsiteType() == self::WEBSITETYPE_TWIG) {
            $output .= PHP_EOL;
            $output .= <<<END
        location ~ ^/(css|images)/(.+) {
                root {$this->baseDir}/www/twig;
        }
END;
        }

        if ($this->getWebsiteType() == self::WEBSITETYPE_TWIG
            || $this->getWebsiteType() == self::WEBSITETYPE_TWIG_CUSTOM) {
            $output .= PHP_EOL;
            $output .= <<<END
        location / {
            try_files \$uri \$uri/ @www;
        }

        location @www {
            rewrite ^/(.*)$ /index.php?$1 last;
        }
END;
        }

        if ($this->getWebsiteType() == self::WEBSITETYPE_TWIG
            || $this->getWebsiteType() == self::WEBSITETYPE_TWIG_CUSTOM) {
            $output .= PHP_EOL;
            $output .= <<<END
        location ~ \.php$ {
                include fastcgi_params;
                fastcgi_pass  127.0.0.1:9000;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
                fastcgi_param USERNAME {$this->user};
        }
END;
        }

//        root   {$this->baseDir}/../wordpress; // TODO

//        location / {
//            index  index.php;
//                rewrite ^.*/files/(.*) /wp-includes/ms-files.php?file=$1;
//
//                if (!-e \$request_filename) {
//                    rewrite ^.+?(/wp-.*) $1 last;
//                        rewrite ^.+?(/.*\.php)$ $1 last;
//                        rewrite ^ /index.php last;
//                }
//        }
/*

        location ~ \.php$ {
                include fastcgi_params;
                fastcgi_pass  127.0.0.1:9000;
                fastcgi_index index.php;
                fastcgi_param  SCRIPT_FILENAME \$document_root/wordpress\$fastcgi_script_name;
                #fastcgi_param  SCRIPT_FILENAME {$this->baseDir}/wordpress\$fastcgi_script_name;
        }
         */

        $output .= PHP_EOL;
        $output .= "}";

        return $output;
    }

    public function create()
    {
        return file_put_contents($this->getFilename(), "$this");
    }

    public function delete()
    {
        if ($this->exists()) {
            return unlink($this->getFilename());
        }

        return false;
    }

    public function getFilename()
    {
        return Str::finish($this->baseDir, '/') . Str::finish($this->confDir, '/') . $this->server_name . ".conf";
    }

    public function exists()
    {
        $file = $this->getFilename();

        return file_exists($file) && is_readable($file);
    }

    public function setLimitRate($rate = '368k')
    {
        $this->limit_rate = $rate;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $baseDir
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @param string $confDir
     */
    public function setConfDir($confDir)
    {
        $this->confDir = $confDir;
    }

    /**
     * @param string $logDir
     */
    public function setLogDir($logDir)
    {
        $this->logDir = $logDir;
    }

    public function setSecurePort()
    {
        $this->port = self::PORT_HTTPS;
    }

    private function getUserPath($username)
    {
        $username = strtolower($username);

        return DIRECTORY_SEPARATOR . $username[0] . DIRECTORY_SEPARATOR . $username[1] . DIRECTORY_SEPARATOR
                . $username[2] . DIRECTORY_SEPARATOR . $username;
    }

    /**
     * @return string
     */
    public function getWebsiteType()
    {
        return $this->websiteType;
    }

    /**
     * @param string $websiteType
     */
    public function setWebsiteType($websiteType)
    {
        $this->websiteType = $websiteType;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrlBlog()
    {
        return $this->redirectUrlBlog;
    }

    /**
     * @param mixed $redirectUrlBlog
     */
    public function setRedirectUrlBlog($redirectUrlBlog)
    {
        $this->redirectUrlBlog = $redirectUrlBlog;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrlFeed()
    {
        return $this->redirectUrlFeed;
    }

    /**
     * @param mixed $redirectUrlFeed
     */
    public function setRedirectUrlFeed(string $feedId, string $redirectUrlFeed)
    {
        $this->redirectUrlFeed[$feedId] = $redirectUrlFeed;
    }

    /**
     * @return string
     */
    public function getConfDir(): string
    {
        return $this->confDir;
    }

    /**
     * @param  bool  $usesProtection
     */
    public function setUsesProtection(bool $usesProtection): void
    {
        $this->usesProtection = $usesProtection;
    }
}
