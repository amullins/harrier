<?php


class SampleSnippet extends Snippet {
    public function SampleSnippet() {
        parent::Snippet('Sample');
        parent::init();
    }

    public function post_page_init() {
        App::store('form', $_POST); // store some data that can be used on the page

        $validation = $this->validateForm();

        if (empty($validation)) {
            App::unstore('form');

            // use App::mail to send an email
        } else {
            App::store('form_validation', $validation);
        }
    }

    private function validateForm() {
        $errors = array();

        if ($_POST['contact-hp'] != '') $errors['honeypot'] = 'Hmmm... Are you a human?';

        return $errors;
    }

    private function datatable($data) {
    	$html = '<table width="80%" cellpadding="5" cellspacing="5" border="0" style="background:#f0f0f0">';
    	foreach ($data as $key => $value) {
    		$html .= "<tr>\n<td valign=\"top\" width=\"15%\"><b>${key}:</b></td>\n<td valign=\"top\">${value}</td></tr>\n";
    	}
    	return $html . '</table>';
    }
}