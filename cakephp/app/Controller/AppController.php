<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

    public function beforeFilter() {
        $user_has_wordpress_editor_role = true;
        if (defined('JAKE')) {
            // Running inside the CakePress plugin
            $this->set('wordpress_user', wp_get_current_user());

            $session = $this->Session->read();
            if (!empty($session['Message'])) {
                define('DONOTCACHEPAGE', 1);
            }
            $user_has_wordpress_editor_role = current_user_can('edit_posts');
        } else {
            // Standalone CakePHP app, not running inside CakePress
        }
        $this->set('user_has_wordpress_editor_role', $user_has_wordpress_editor_role);
    }

    /**
     * If you're using a WordPress page caching plugin (e.g., Quick Cache), you need special handling in the case of a redirect, where you exit without 
     * returning to Wordpress.  In that case, the plugin cannot know not to handle caching the current URL and destination URL.  
     * So clear the destination, and if the current one should be cleared, clear that as well.
     *
     * The following is a sample implementation for Quick Cache.  All this can be ignored if you're not using a page-caching plugin.
     * 
     * @param string $url
     * @param int $status
     * @param bool $exit
     * @return boolean
     */
    public function beforeRedirect($url, $status = null, $exit = true) {
        parent::beforeRedirect($url, $status, $exit);

        if (defined('JAKE') && method_exists('quick_cache', 'clear_by_url')) {
            quick_cache::clear_by_url(Router::url($url));

            if (defined('DONOTCACHEPAGE'))
                quick_cache::clear_by_url($this->here);
        }
        return true;
    }

}
