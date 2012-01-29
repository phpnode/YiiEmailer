<?php
require_once "common.php";
/**
 * Tests for the {@link AEmail} class
 * @author Charles Pick
 * @package packages.email.tests
 */
class AEmailTest extends CTestCase {
	/**
	 * Tests sending an email
	 */
	public function testSend() {
		Yii::app()->email->mailer = new MockEmailer();
		$email = new AEmail();
		$email->subject = "A Test Subject";
		$email->content = "Some content goes here";
		$email->layout = "packages.email.tests.views.testview";
		$this->assertFalse($email->send());
		$email->recipient = "charles.pick@gmail.com";
		$this->assertFalse($email->send());
		$email->sender = "charles.pick@gmail.com";
		$this->assertTrue($email->send());
		$this->assertEquals(1,MockEmailer::$sendCalled);
		$this->assertContains("<h1>Hi charles.pick@gmail.com!</h1>",MockEmailer::$lastRenderResult);
	}
}
/**
 * A mock email sender to use when testing emails.
 * @author Charles Pick
 * @package packages.email.testes
 */
class MockEmailer extends AEmailer {
	/**
	 * Holds the result of the last render
	 * @var string
	 */
	public static $lastRenderResult;
	/**
	 * Holds how often send() has been called
	 * @var integer
	 */
	public static $sendCalled = 0;
	/**
	 * Sends an email
	 * @param AEmail $email the email to send
	 * @return boolean whether the email was sent successfully or not
	 */
	public function send(AEmail $email)
	{
		self::$sendCalled++;
		self::$lastRenderResult = $email->render();
		return true;
	}

}