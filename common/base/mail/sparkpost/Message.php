<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\mail\sparkpost;

use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\mail\MessageInterface;

/**
* Class Message
* @package common\mail\sparkpost
*/
class Message extends \yii\swiftmailer\Message implements MessageInterface
{
    /**
    * @var Mailer
    */
    public $mailer;

    private $__messageParams = [
        'options' => [
            'click_tracking' => true,
            'transactional' => true,
        ],
    ];

    /**
    * @return array
    */
    public function getMessageParams()
    {
        $params = $this->__messageParams;

        if (($from = $this->generateFromData()) !== false) {
            $addresses = ArrayHelper::getColumn($from, 'address');
            ArrayHelper::setValue($params, 'content.from', reset($addresses));
        }

        $recipients = ArrayHelper::getValue($params, 'recipients', []);

        $toAddress = null;
        if (($to = $this->generateToData()) !== false) {
            foreach ($to as $item) {
                $recipients[] = $item;
                if ($toAddress === null) {
                    $toAddress = ArrayHelper::getValue($item, 'address.email');
                }
            }
        }

        if (($cc = $this->generateCcData()) !== false) {
            foreach ($cc as $item) {
                if (isset($toAddress)) {
                    $item['header_to'] = $toAddress;
                }

                $recipients[] = $item;
            }
        }

        if (($bcc = $this->generateBccData()) !== false) {
            foreach ($bcc as $item) {
                if (isset($toAddress)) {
                    $item['header_to'] = $toAddress;
                }

                $recipients[] = $item;
            }
        }

        ArrayHelper::setValue($params, 'recipients', $recipients);

        return $params;
    }


    /**
    * @return array
    */
    protected function generateFromData()
    {
        $value = $this->getFrom();
        if (empty($value)) {
            if ($this->mailer instanceof Mailer) {
                $config = $this->mailer->messageConfig;
                if (isset($config['from'])) {
                    $value = $config['from'];
                }
            }
        }

        return $this->generateRecipientData($value);
    }

    /**
    * @return array
    */
    protected function generateToData()
    {
        $value = $this->getTo();
        return $this->generateRecipientData($value);
    }

    /**
    * @return array
    */
    protected function generateCcData()
    {
        $value = $this->getCc();
        return $this->generateRecipientData($value);
    }

    /**
    * @return array
    */
    protected function generateBccData()
    {
        $value = $this->getBcc();
        return $this->generateRecipientData($value);
    }

    /**
    * @param string|array $value
    * @return array|false
    */
    protected function generateRecipientData($value)
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $email => $name) {
                if (empty($name)) {
                    $data[] = ['address' => ['email' => $email]];
                } else {
                    $data[] = ['address' => ['email' => $email, 'name' => $name]];
                }
            }
        } elseif (!empty($value)) {
            $data[] = ['address' => ['email' => $value]];
        }
        return empty($data) ? false : $data;
    }


    /**
    * Returns the message subject.
    * @return string the message subject
    */
    public function getSubject()
    {
        return ArrayHelper::getValue($this->__messageParams, 'content.subject');
    }

    /**
    * Sets the message subject.
    * @param string $subject message subject
    * @return $this self reference.
    */
    public function setSubject($subject)
    {
        ArrayHelper::setValue($this->__messageParams, 'content.subject', $subject);
        return $this;
    }

    /**
    * Sets message plain text content.
    * @param string $text message plain text content.
    * @return $this self reference.
    */
    public function setTextBody($text)
    {
        ArrayHelper::setValue($this->__messageParams, 'content.text', $text);
        return $this;
    }

    /**
    * Sets message HTML content.
    * @param string $html message HTML content.
    * @return $this self reference.
    */
    public function setHtmlBody($html)
    {
        ArrayHelper::setValue($this->__messageParams, 'content.html', $html);
        return $this;
    }

    /**
    * Attaches existing file to the email message.
    * @param string $fileName full file name
    * @param array $options options for embed file. Valid options are:
    *
    * - fileName: name, which should be used to attach file.
    * - contentType: attached file MIME type.
    *
    * @return $this self reference.
    */
    public function attach($fileName, array $options = [])
    {
        $attachment = $this->resolveFilePath($fileName, isset($options['fileName']) ? $options['fileName'] : null);
        return $this->addAttachment($attachment);
    }

    /**
    * Attach specified content as file for the email message.
    * @param string $content attachment file content.
    * @param array $options options for embed file. Valid options are:
    *
    * - fileName: name, which should be used to attach file.
    * - contentType: attached file MIME type.
    *
    * @return $this self reference.
    */
    public function attachContent($content, array $options = [])
    {
        $attachment = $this->resolveFileContent($content, isset($options['fileName']) ? $options['fileName'] : null);
        return $this->addAttachment($attachment);
    }

    /**
    * Attach a file and return it's CID source.
    * This method should be used when embedding images or other data in a message.
    * @param string $fileName file name.
    * @param array $options options for embed file. Valid options are:
    *
    * - fileName: name, which should be used to attach file.
    * - contentType: attached file MIME type.
    *
    * @return string attachment CID.
    */
    public function embed($fileName, array $options = [])
    {
        $inline = $this->resolveFilePath($fileName, isset($options['fileName']) ? $options['fileName'] : null);
        return $this->addInline($inline);
    }

    /**
    * Attach a content as file and return it's CID source.
    * This method should be used when embedding images or other data in a message.
    * @param string $content attachment file content.
    * @param array $options options for embed file. Valid options are:
    *
    * - fileName: name, which should be used to attach file.
    * - contentType: attached file MIME type.
    *
    * @return string attachment CID.
    */
    public function embedContent($content, array $options = [])
    {
        $inline = $this->resolveFileContent($content, isset($options['fileName']) ? $options['fileName'] : null);
        return $this->addInline($inline);
    }

    /**
    * Returns string representation of this message.
    * @return string the string representation of this message.
    */
    public function toString()
    {
        return VarDumper::dumpAsString($this->__messageParams);
    }

    /**
    * @param array $attachment
    * @return $this
    */
    protected function addAttachment($attachment)
    {
        $attachments = ArrayHelper::getValue($this->__messageParams, 'content.attachments', []);
        $attachments[] = $attachment;
        ArrayHelper::setValue($this->__messageParams, 'content.attachments', $attachments);

        return $this;
    }

    /**
    * @param array $inline
    * @return $this
    */
    protected function addInline($inline)
    {
        $attachments = ArrayHelper::getValue($this->__messageParams, 'content.inline_images', []);
        $attachments[] = $inline;
        ArrayHelper::setValue($this->__messageParams, 'content.inline_images', $attachments);

        return $this;
    }

    /**
    * @param string $path
    * @param string $name
    * @return array
    */
    protected function resolveFilePath($path, $name)
    {
        $file = ['name' => $name];

        if (is_file($path)) {
            $file['type'] = mime_content_type($path);
            $content = file_get_contents($path);
            $file['data'] = base64_encode($content);
        } else {
            throw new InvalidArgumentException('Invalid $path: ' . $path);
        }

        return $file;
    }

    /**
    * @param string $content
    * @param string $name
    * @return array
    */
    protected function resolveFileContent($content, $name)
    {
        $file = ['name' => $name];

        if (is_string($content)) {
            $folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . '_alps';
            FileHelper::createDirectory($folder);
            $tempFile = tempnam($folder, '_mailer');
            file_put_contents($tempFile, $content);

            $file['type'] = mime_content_type($tempFile);
            $file['data'] = base64_encode($content);

            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        } else {
            throw new InvalidArgumentException('Invalid $content !!');
        }

        return $file;
    }
}
