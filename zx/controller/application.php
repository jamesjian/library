<?php
namespace Zx\Controller;
use \Zx\Session\Session;
use \Zx\Message\Message;
use \Zx\View\View;
use \Zx\Test\Test;
/**
 */
class Application {

    /**
      currently only use standard url:
     * module/controller/action/param1/param2/....
     */
    public static function run() {
        //analyze url, get module, controller, action and params
        //put it into application
        $handler = new Session();
        session_set_save_handler($handler, true);
        session_start();
        //Message::init_message();
        Route::analyze_url();
        //controller object
        $controller_name = Route::get_controller_class();
        $controller = new $controller_name;
        //always have init()
        $controller->init();
        //action method
        $action_name = Route::get_action();
        //call this method
        $controller->$action_name();
        $content = '';
        //get view file, it's set in action method
        $view_file = View::get_view_file();
        if ($view_file <> '') {
        //populate action variables into view file, they're set in action method
        extract(View::get_action_variables());

        ob_start();
        include $view_file;
        //must use $content as variable name, because it's used in template file
        $content = ob_get_contents();
        ob_end_clean();
        }
        $result = $content;
        //if use template
        if (View::wheather_use_template()) {
            //get template file 
            $template_file = View::get_template_file();
            if ($template_file <> '') {
            //populate template variables into template file, they're set in init method
            extract(View::get_template_variables());
            ob_start();
            include $template_file;
            $result = ob_get_contents();
            ob_end_clean();
            }
        } else {
            //for ajax no template 
        }
        
        //output it
        echo $result;
        
    }
	/**
		usually no view 
	*/
	public static function cron_run()
	{
	
	}

}