<?php
/**
 * User: fabio
 * Date: 31.10.20
 * Time: 14:37
 */

namespace App\Classes;


class LoginLegacyInit
{
    public static function run()
    {
        if (!defined('SGL_INITIALISED')) {
            \SGL_FrontController::init();
        }
        //  assign request to registry
        $input = \SGL_Registry::singleton();
        $req = \SGL_Request::singleton();

        $input->setRequest($req);

        $outputClass = \SGL_FrontController::getOutputClass();
        $output = new $outputClass();

        // run module init tasks
        \SGL_Task_InitialiseModules::run();

        $process =  new \SGL_Task_Init(
            new \SGL_Task_DiscoverClientOs(
                new \SGL_Void()
            )
        );

        // Kann
        /*        $process =  new \SGL_Task_Init(
                    new \SGL_Task_DiscoverClientOs(
                        new \SGL_Task_SetupLangSupport2(
                            new \SGL_Void()
                        )));*/

        $process->process($input, $output);
    }
}
