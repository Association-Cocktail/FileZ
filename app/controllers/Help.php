<?php

/**
 * @file
 * Short description.
 * 
 * Long description.
 * 
 * @package FileZ
 */

/**
 * Controller used to generate user documentation from markdown files
 */
class App_Controller_Help extends Fz_Controller {

    /**
     *
     */
    public function indexAction () {
        return $this->showPage ('index');
    }

    /**
     *
     */
    public function termsAction () {
        return $this->showPage ('terms');
    }

    /**
     * Report Bug by mail (show email form only)
     */
    public function emailFormAction () {
        return html ('report/email.php');
    }

    /**
     * Share a file url by mail
     */
    public function emailAction () {
        // Send mails
		$mail = $this->createMail();
        $emailValidator = new Zend_Validate_EmailAddress();
        $email = $_POST['email'];
		if ($emailValidator->isValid ($email)){
			$mail->setReplyTo  ($email);
			$mail->clearFrom();
			$mail->setFrom     ($email);
		}
		else {
			$msg = __r('Email address "%email%" is incorrect, please correct it.',
				array ('email' => $email));
			return $this->returnError ($msg, 'report/email.php');
		}
        $subject = __('[FileZ] Bug report');
        $msg = $_POST ['msg'];

        $mail->setBodyText ($msg);
        $mail->setSubject  ($subject);
		$mail->addTo ('notification@asso-cocktail.fr');


        try {
            $mail->send ();
            return $this->returnSuccessOrRedirect ('/');
        }
        catch (Exception $e) {
            fz_log ('Error while sending email', FZ_LOG_ERROR, $e);
            $msg = __('An error occurred during email submission, probably too many emails. Please try again.');
            return $this->returnError ($msg, 'report/email.php');
        }
    }



    /**
     *
     */
    public function showPageAction () {
        return $this->showPage (params ('page'));
    }

    private function returnError ($msg, $template) {
        if ($this->isXhrRequest ()) {
            return json (array (
                'status' => 'error',
                'statusText' => $msg
            ));
        } else {
            flash_now ('error', $msg);
            return html ($template);
        }
    }
    private function returnSuccessOrRedirect ($url) {
        if ($this->isXhrRequest ()) {
            return json (array ('status' => 'success'));
        } else {
            flash ('notification', __('Successfully sent.'));
            redirect_to ($url);
        }
    }


    /**
     *
     */
    protected function showPage ($pageName) {
        $locale = option ('locale')->getLanguage ();
        $filename = str_replace ('_', '/', $pageName);
        $filename = option ('root_dir').'/doc/user/'.$locale.'/'.$filename.'.txt';

        if (file_exists ($filename)) {
            ob_start();
            include $filename;
            return html (Markdown (ob_get_clean()), 'layout/doc.html.php');
        } else {
            return halt (NOT_FOUND, __('This documentation does not exist'));
        }
    }
}
