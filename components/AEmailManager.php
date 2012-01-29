<?php
Yii::import("packages.email.mailers.*");
/**
 * Provides email sending functionality
 * @author Charles Pick
 * @package packages.email
 */
class AEmailManager extends CApplicationComponent {
	/**
	 * Holds the email sender
	 * @var AEmailMailer
	 */
	protected $_mailer;

	/**
	 * The default sender
	 * @var string
	 */
	protected $_defaultSender;

	/**
	 * Gets the mailer to use when sending email
	 * @return AEmailMailer the email mailer to use
	 */
	public function getMailer() {
		if ($this->_mailer === null) {
			$this->_mailer = new APHPEmailer;
		}
		return $this->_mailer;
	}
	/**
	 * Sets the email sender
	 * @param array|AEmailMailer either an email sender instance or the configuration for one
	 * @return AEmailMailer the email sender
	 */
	public function setMailer($sender) {
		if (!is_object($sender) || !($sender instanceof AEmailer)) {
			$config = $sender;
			if (isset($config['class'])) {
				$sender = new $config['class'];
				unset($config['class']);
			}
			else {
				$sender = new APHPEmailer;
			}
			foreach($config as $key => $value) {
				$sender->{$key} = $value;
			}
		}
		$this->_mailer = $sender;
		return $sender;
	}

	/**
	 * Sets the email address of the default sender.
	 * @param string $defaultSender the default sender's email address
	 */
	public function setDefaultSender($defaultSender)
	{
		$this->_defaultSender = $defaultSender;
	}

	/**
	 * Gets the default sender's email address
	 * @return string the default email address
	 */
	public function getDefaultSender()
	{
		if ($this->_defaultSender === null) {
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			$this->_defaultSender = Yii::app()->name." <noreply@".$host.">";
		}
		return $this->_defaultSender;
	}

}
