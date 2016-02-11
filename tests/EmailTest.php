<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Tomaj\ImapMailDownloader\Email;

class EmailTest extends PHPUnit_Framework_TestCase
{
    public function testCreationWithouOptionalAttributes()
    {
        $data = new stdClass;
        $data->from = 'from@asdsad.sk';
        $data->to = 'asdsad@adsad.sk';
        $data->date = '2014-01-02 14:34';
        $data->message_id = 'sa09uywqet09u3t';
        $data->size = 125;
        $data->uid = '236-0982369034856';
        $data->msgno = 4125;
        $data->recent = 1;
        $data->flagged = 0;
        $data->answered = 1;
        $data->deleted = 0;
        $data->seen = 1;
        $data->draft = 1;
        $data->text = "text";

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

        $email = new Email(array($data), $body);

        $this->assertEquals($email->getFrom(), 'from@asdsad.sk');
        $this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
        $this->assertEquals($email->getDate(), '2014-01-02 14:34');
        $this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
        $this->assertEquals($email->getReferences(), null);
        $this->assertEquals($email->getInReplyTo(), null);
        $this->assertEquals($email->getSize(), 125);
        $this->assertEquals($email->getUid(), '236-0982369034856');
        $this->assertEquals($email->getMsgNo(), 4125);
        $this->assertEquals($email->getRecent(), 1);
        $this->assertEquals($email->getFlagged(), 0);
        $this->assertEquals($email->getAnswered(), 1);
        $this->assertEquals($email->getDeleted(), 0);
        $this->assertEquals($email->getSeen(), 1);
        $this->assertEquals($email->getDraft(), 1);

        $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');

        $this->assertEquals($email->getHeaders(), NULL);
    }

    public function testCreationWithAllAttributes()
    {
        $data = new stdClass;
        $data->from = 'from@asdsad.sk';
        $data->to = 'asdsad@adsad.sk';
        $data->date = '2014-01-02 14:34';
        $data->message_id = 'sa09uywqet09u3t';
        $data->references = 'asdas09uyfei9f';
        $data->in_reply_to = '135325325325';
        $data->size = 125;
        $data->uid = '236-0982369034856';
        $data->msgno = 4125;
        $data->recent = 1;
        $data->flagged = 0;
        $data->answered = 1;
        $data->deleted = 0;
        $data->seen = 1;
        $data->draft = 1;

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

        $email = new Email(array($data), $body);

        $this->assertEquals($email->getFrom(), 'from@asdsad.sk');
        $this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
        $this->assertEquals($email->getDate(), '2014-01-02 14:34');
        $this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
        $this->assertEquals($email->getReferences(), 'asdas09uyfei9f');
        $this->assertEquals($email->getInReplyTo(), '135325325325');
        $this->assertEquals($email->getSize(), 125);
        $this->assertEquals($email->getUid(), '236-0982369034856');
        $this->assertEquals($email->getMsgNo(), 4125);
        $this->assertEquals($email->getRecent(), 1);
        $this->assertEquals($email->getFlagged(), 0);
        $this->assertEquals($email->getAnswered(), 1);
        $this->assertEquals($email->getDeleted(), 0);
        $this->assertEquals($email->getSeen(), 1);
        $this->assertEquals($email->getDraft(), 1);

        $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');

        $this->assertEquals($email->getHeaders(), NULL);
    }


    public function testCreationWithOverviewOnly()
    {
        $data = new stdClass;
        $data->from = 'from@asdsad.sk';
        $data->to = 'asdsad@adsad.sk';
        $data->date = '2014-01-02 14:34';
        $data->message_id = 'sa09uywqet09u3t';
        $data->references = 'asdas09uyfei9f';
        $data->in_reply_to = '135325325325';
        $data->size = 125;
        $data->uid = '236-0982369034856';
        $data->msgno = 4125;
        $data->recent = 1;
        $data->flagged = 0;
        $data->answered = 1;
        $data->deleted = 0;
        $data->seen = 1;
        $data->draft = 1;

        $email = new Email(array($data));

        $this->assertEquals($email->getFrom(), 'from@asdsad.sk');
        $this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
        $this->assertEquals($email->getDate(), '2014-01-02 14:34');
        $this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
        $this->assertEquals($email->getReferences(), 'asdas09uyfei9f');
        $this->assertEquals($email->getInReplyTo(), '135325325325');
        $this->assertEquals($email->getSize(), 125);
        $this->assertEquals($email->getUid(), '236-0982369034856');
        $this->assertEquals($email->getMsgNo(), 4125);
        $this->assertEquals($email->getRecent(), 1);
        $this->assertEquals($email->getFlagged(), 0);
        $this->assertEquals($email->getAnswered(), 1);
        $this->assertEquals($email->getDeleted(), 0);
        $this->assertEquals($email->getSeen(), 1);
        $this->assertEquals($email->getDraft(), 1);

        $this->assertEquals($email->getBody(), NULL);

        $this->assertEquals($email->getHeaders(), NULL);
    }

    public function testCreationWithBodyOnly(){

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

        $email = new Email(NULL,$body);

        $this->assertEquals($email->getFrom(), NULL);
        $this->assertEquals($email->getTo(), NULL);
        $this->assertEquals($email->getDate(), NULL);
        $this->assertEquals($email->getMessageId(), NULL);
        $this->assertEquals($email->getReferences(), NULL);
        $this->assertEquals($email->getInReplyTo(), NULL);
        $this->assertEquals($email->getSize(), NULL);
        $this->assertEquals($email->getUid(), NULL);
        $this->assertEquals($email->getMsgNo(), NULL);
        $this->assertEquals($email->getRecent(), NULL);
        $this->assertEquals($email->getFlagged(), NULL);
        $this->assertEquals($email->getAnswered(), NULL);
        $this->assertEquals($email->getDeleted(), NULL);
        $this->assertEquals($email->getSeen(), NULL);
        $this->assertEquals($email->getDraft(), NULL);

        $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');

        $this->assertEquals($email->getHeaders(), NULL);
    }


    public function testCreationWithHeadersOnly(){

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

        $email = new Email(NULL,$body);

        $this->assertEquals($email->getFrom(), NULL);
        $this->assertEquals($email->getTo(), NULL);
        $this->assertEquals($email->getDate(), NULL);
        $this->assertEquals($email->getMessageId(), NULL);
        $this->assertEquals($email->getReferences(), NULL);
        $this->assertEquals($email->getInReplyTo(), NULL);
        $this->assertEquals($email->getSize(), NULL);
        $this->assertEquals($email->getUid(), NULL);
        $this->assertEquals($email->getMsgNo(), NULL);
        $this->assertEquals($email->getRecent(), NULL);
        $this->assertEquals($email->getFlagged(), NULL);
        $this->assertEquals($email->getAnswered(), NULL);
        $this->assertEquals($email->getDeleted(), NULL);
        $this->assertEquals($email->getSeen(), NULL);
        $this->assertEquals($email->getDraft(), NULL);

        $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');

        $this->assertEquals($email->getHeaders(), NULL);
    }

    public function testCreationWithAllParams(){

        $data = new stdClass;
        $data->from = 'from@asdsad.sk';
        $data->to = 'asdsad@adsad.sk';
        $data->date = '2014-01-02 14:34';
        $data->message_id = 'sa09uywqet09u3t';
        $data->references = 'asdas09uyfei9f';
        $data->in_reply_to = '135325325325';
        $data->size = 125;
        $data->uid = '236-0982369034856';
        $data->msgno = 4125;
        $data->recent = 1;
        $data->flagged = 0;
        $data->answered = 1;
        $data->deleted = 0;
        $data->seen = 1;
        $data->draft = 1;

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';

        $headers = '1234567890 8yc81bch2zzxkjtyp8eraqziaou';

        $email = new Email(array($data), $body, $headers);

        $this->assertEquals($email->getFrom(), 'from@asdsad.sk');
        $this->assertEquals($email->getTo(), 'asdsad@adsad.sk');
        $this->assertEquals($email->getDate(), '2014-01-02 14:34');
        $this->assertEquals($email->getMessageId(), 'sa09uywqet09u3t');
        $this->assertEquals($email->getReferences(), 'asdas09uyfei9f');
        $this->assertEquals($email->getInReplyTo(), '135325325325');
        $this->assertEquals($email->getSize(), 125);
        $this->assertEquals($email->getUid(), '236-0982369034856');
        $this->assertEquals($email->getMsgNo(), 4125);
        $this->assertEquals($email->getRecent(), 1);
        $this->assertEquals($email->getFlagged(), 0);
        $this->assertEquals($email->getAnswered(), 1);
        $this->assertEquals($email->getDeleted(), 0);
        $this->assertEquals($email->getSeen(), 1);
        $this->assertEquals($email->getDraft(), 1);

        $this->assertEquals($email->getBody(), 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg');

        $this->assertEquals($email->getHeaders(), '1234567890 8yc81bch2zzxkjtyp8eraqziaou');
    }


    public function testGetSourceRequirements(){

        $body = 'asf098ywetoiuwhegt908weg ewfg dsyfg089dsyfg';
        $headers = '1234567890 8yc81bch2zzxkjtyp8eraqziaou';

        $email = new Email(NULL, $body, NULL);
        try {
            $email->getSource();
            $this->fail("if headers are passed at creation time an Exception should be thrown");
        } catch(Exception $e){
            // test passed
        }

        $mail = new Email(NULL, NULL, $headers);
        try {
            $email->getSource();
            $this->fail("if body is not passed at creation time an Exception should be thrown");
        } catch(Exception $e){
            // test passed
        }
    }
}
