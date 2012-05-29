<?php
/**
 * Sends email using PHP's built in mail functions
 * @author Charles Pick
 * @package packages.email.mailers
 */
class APHPEmailer extends AEmailer {
	/**
	 * Sends an email
	 * @param AEmail $email the email to send
	 * @return boolean whether the email was send successfully or not
	 */
	public function send(AEmail $email) {
		$headers = $this->buildHeaders($email);
		if (count($email->attachments))
			$message = $this->encodeMessageWithAttachments($email);
		else
			$message = $email->render();


		return mail($email->recipient,$email->subject,$message,$headers);
	}

	/**
	 * Encodes an email message with attachment(s) before sending
	 * @param AEmail $email the email being sent
	 * @return string the encoded message
	 */
	protected function encodeMessageWithAttachments(AEmail $email) {
		$encoded = array();
		$encoded[] = "--".$email->getUniqueId();
		$encoded[] = "Content-Type: ".($email->isHtml ? "text/html" : "text/plain")."; charset=".Yii::app()->charset;
		$encoded[] = "Content-Transfer-Encoding: base64";
		$encoded = implode("\r\n",$encoded)."\r\n";
		$encoded .= chunk_split(base64_encode($email->render()));
		foreach($email->attachments as $attachment) {
			$encoded .= $this->encodeAttachment($attachment,$email);
		}
		$encoded .= "--".$email->getUniqueId()."--";
		return $encoded;
	}

	/**
	 * Builds the headers to send along with the email
	 * @param AEmail $email the email to send
	 * @return string the headers to send
	 */
	protected function buildHeaders(AEmail $email) {
		$headers = array();
		$headers[] = "From: ".$email->sender;
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "X-Mailer: PHP";
		$headers[] = "Reply-To: ".$email->sender;
		$headers[] = "Return-Path: ".$email->sender;
		if ($email->cc != "") {
			$headers[] = "CC: ".$email->cc;
		}
		if ($email->bcc != "") {
			$headers[] = "BCC: ".$email->bcc;
		}
		if (count($email->attachments))
			$headers[] = "Content-Type: multipart/mixed; boundary = ".$email->getUniqueId();
		else
			$headers[] = "Content-Type: ".($email->isHtml ? "text/html" : "text/plain")."; charset=".Yii::app()->charset;
		return implode("\r\n",$headers)."\r\n";
	}

	/**
	 * Encodes an attachment for sending
	 * @param AResource $attachment The attachment to encode
	 * @param AEmail $email The email being sent
	 * @return string the encoded attachment
	 */
	protected function encodeAttachment(AResource $attachment, AEmail $email) {
		$encoded = array();
		$encoded[] = "--".$email->getUniqueId();
		$encoded[] = "Content-Type: ".$attachment->type."; name=\"".$attachment->name."\"";
		$encoded[] = "Content-Transfer-Encoding: base64";
		$encoded[] = "Content-Disposition: attachment";
		$encoded = implode("\r\n",$encoded)."\r\n";
		$encoded .= chunk_split(base64_encode($attachment->getContent()));
		return $encoded;
	}
}
