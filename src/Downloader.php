<?php

namespace Tomaj\ImapMailDownloader;

/**
 * Class Downloader
 * @package Tomaj\ImapMailDownloader
 */
class Downloader
{
    /**
     * @const Fetch email overview
     * @see Downloader::fetch()
     */
    const FETCH_OVERVIEW    = 1;

    /**
     * @const Fetch email full headers part
     * @see Downloader::fetch()
     */
    const FETCH_HEADERS     = 2;

    /**
     * @const Fetch email body
     * @see Downloader::fetch()
     */
    const FETCH_BODY        = 4;

    /**
     * @const Fetch email full headers and body (equal to FETCH_HEADERS | FETCH_BODY
     * @see Downlaoder::fetch()
     */
    const FETCH_SOURCE      = 6;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;


    /**
     * @var string
     */
    private $inboxFolder = 'INBOX';

    /**
     * @var ProcessAction
     */
    private $defaultProcessAction;

    /**
     * @var bool
     * @see Downloader::setProcessedFoldersAutomake()
     */
    private $processedFoldersAutomake = true;

    /**
     * @var bool|array
     * @see Downloader::getAlerts()
     */
    private $alerts = false;

    /**
     * @var bool|array
     * @see Downloader::getErrors()
     */
    private $errors = false;


    /**
     * Downloader constructor.
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @param null $defaultProcessAction
     * @see ProcessAction
     */
    public function __construct($host, $port, $username, $password, $defaultProcessAction = null)
    {
        if (!extension_loaded('imap')) {
            throw new \Exception('Extension \'imap\' must be loaded');
        }

        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        // if no valid default process action was passed, set up a predefined one
        if ($defaultProcessAction !== null and $defaultProcessAction instanceof ProcessAction) {
            $this->defaultProcessAction = $defaultProcessAction;
        } else {
            $this->defaultProcessAction = ProcessAction::move('INBOX/processed');
        }
    }

    /**
     * Set inbox folder
     * @param string $inboxFolder
     * @default "INBOX"
     * @return $this
     */
    public function setInboxFolder($inboxFolder = 'INBOX')
    {
        $this->inboxFolder = $inboxFolder;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setProcessedFoldersAutomake($enabled)
    {
        $this->processedFoldersAutomake = $enabled;
        return $this;
    }

    /**
     * @param ProcessAction $processAction
     * @return $this
     * @throws \Exception
     */
    public function setDefaultProcessAction(ProcessAction $processAction)
    {
        if ($processAction === null or !($processAction instanceof  ProcessAction)) {
            throw new \Exception('Default processed action is invalid!');
        }
        $this->defaultProcessAction = $processAction;
        return $this;
    }

    /** Get IMAP alerts
     * @return array|bool
     * @see imap_alerts()
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /** Get IMAP errors
     * @return array|bool
     * @see imap_errors()
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function fetch(MailCriteria $criteria, $callback, $fetchParts = null)
    {
        $HOST = '{' . $this->host . ':' . $this->port . '}';
        $INBOX = $HOST . $this->inboxFolder;

        if ($fetchParts === null) {
            $fetchParts = self::FETCH_OVERVIEW | self::FETCH_BODY;
        }

        $exception = null;
        $mailbox = null;
        try {
            $mailbox = imap_open($INBOX, $this->username, $this->password);
            if (!$mailbox) {
                throw new ImapException("Cannot connect to imap server: {$HOST}'");
            }


            // if default folder is set, check for its existence
            if ($this->defaultProcessAction->getProcessedFolder() !== null) {
                $this->checkProcessedFolder(
                    $mailbox,
                    $this->defaultProcessAction->getProcessedFolder(),
                    $this->processedFoldersAutomake
                );
            }

            $emails = $this->fetchEmails($mailbox, $criteria);

            if ($emails) {
                foreach ($emails as $emailIndex) {

                    // fetch only wanted parts
                    $overview = $fetchParts & self::FETCH_OVERVIEW ? imap_fetch_overview($mailbox, $emailIndex, 0) : null;
                    $headers = $fetchParts & self::FETCH_HEADERS ? imap_fetchheader($mailbox, $emailIndex, 0) : null;
                    $body = $fetchParts & self::FETCH_BODY ? imap_body($mailbox, $emailIndex) : null;

                    // construct email object with retrieved parts
                    $email = new Email($overview, $body, $headers);

                    // call user supplied callback with given email
                    $processAction = $callback($email);

                    // bring returned process action into one of two formats: false or an ProcessAction object
                    // thereby apply a meaningful transformation logic
                    if (is_bool($processAction) and $processAction) {
                        $processAction = $this->defaultProcessAction;
                    } elseif (is_callable($processAction)) {
                        $processAction = ProcessAction::callback($processAction);
                    } elseif (is_string($processAction)) {
                        switch ($processAction) {
                            case ProcessAction::ACTION_MOVE:
                                $processAction = ProcessAction::move($this->defaultProcessAction->getProcessedFolder());
                                break;

                            case ProcessAction::ACTION_DELETE:
                                $processAction = ProcessAction::delete();
                                break;

                            case ProcessAction::ACTION_CALLBACK:
                                $processAction = ProcessAction::callback($this->$this->defaultProcessedAction->getCallback());
                                break;

                            default:
                                throw \Exception("Unexpected process action: {$processAction}");
                        }
                    }

                    // only process if ProcessAction object, ie if not false
                    if ($processAction instanceof ProcessAction) {

                        switch ($processAction->getAction()) {
                            case ProcessAction::ACTION_MOVE:
                                $this->checkProcessedFolder(
                                    $mailbox,
                                    $processAction->getProcessedFolder(),
                                    $this->processedFoldersAutomake
                                );
                                $res = imap_mail_move($mailbox, $emailIndex, $processAction->getProcessedFolder());
                                if (!$res) {
                                    throw new \Exception("Unexpected error: Cannot move email to ".$processAction->getProcessedFolder());
                                    break;
                                }
                                break;

                            case ProcessAction::ACTION_DELETE:
                                $res = imap_delete($mailbox, $emailIndex);
                                if (!$res) {
                                    throw new \Exception("Unexpected error: Cannot delete email.");
                                }
                                break;

                            case ProcessAction::ACTION_CALLBACK:
                                call_user_func_array($processAction->getCallback(), array($mailbox, $emailIndex));
                                break;
                        }

                    }
                }
            }
        } catch (\Exception $e) {
            // exceptions will be thrown at end of method, but to suppress any unwanted output and
            // properly close any imap resource do not throw immediately
            $exception = $e;
        }

        $this->alerts = imap_alerts();
        $this->errors = imap_errors();

        if (is_resource($mailbox)) {
            imap_close($mailbox);
        }

        // because finally statements are only supported from PHP5.5+ this is more of a workaround..
        if ($exception !== null) {
            throw $exception;
        }
    }

    /** Checks the existence of a folder on the imap server, and optionally creates it
     * @param $mailbox  Imap mailbox resource
     * @param $processedFolder name of folder to check and optionally create
     * @param bool $automake
     * @throws \Exception
     */
    private function checkProcessedFolder($mailbox, $processedFolder, $automake = false)
    {
        $HOST = '{' . $this->host . ':' . $this->port . '}';
        $list = imap_getmailboxes($mailbox, $HOST, $processedFolder);
        if (count($list) == 0) {
            if ($automake) {
                imap_createmailbox($mailbox, $processedFolder);
            } else {
                throw new \Exception("You need to create imap folder '{$processedFolder}'");
            }
        }
    }

    /** Returns list of email indices that match the search criteria
     * @param $mailbox Imap mailbox resource
     * @param $criteria
     * @return array|bool
     */
    private function fetchEmails($mailbox, $criteria)
    {
        $emails = imap_search($mailbox, $criteria->getSearchString());
        if (!$emails) {
            return false;
        }
        rsort($emails);
        return $emails;
    }
}
