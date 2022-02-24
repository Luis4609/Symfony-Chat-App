<?php
// tests/Controller/MailControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    /*
    * Test for the registration email
    */
    public function testMailIsSentAndContentIsOk()
    {
        $client = $this->createClient();
        $client->request('GET', 'app_verify_email');
        $this->assertResponseIsSuccessful();

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'Welcome');
        $this->assertEmailTextBodyContains($email, 'Welcome');
    }
}
