<?php
/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class ConfigName
 * @package app\base\enum
 */
class ConfigName extends BaseEnum
{
    //------- General Setup -------//
    const FACEBOOK_APP_ID = 'facebook.app.id';
    const FACEBOOK_APP_SECRET = 'facebook.app.secret';

    const OTP_PROVIDER = 'otp.provider';

    //------- App Store -------//
    const PLAY_STORE_ENABLED = 'google.play.store.enabled';
    const PLAY_STORE_URL= 'google.play.store.url';
    const APP_STORE_ENABLED = 'apple.app.store.enabled';
    const APP_STORE_URL= 'apple.app.store.url';

    const ENFORCE_SUPER_ADMIN_MFA = 'enforce.super.admin.mfa';

    //------- GOOGLE -------//
    const GOOGLE_MAP_API_KEY = 'google.map.api.key';

    //------- MAILER -------//
    const MAILER_DRIVER = 'mailer.driver';
    const MAILER_SENDER_NAME = 'mailer.sender.name';
    const MAILER_SENDER_EMAIL = 'mailer.sender.email';
    const MAILER_SMTP_DOMAIN = 'mailer.smtp.domain';
    const MAILER_SMTP_SERVER = 'mailer.smtp.server';
    const MAILER_SMTP_USERNAME = 'mailer.smtp.username';
    const MAILER_SMTP_PASSWORD = 'mailer.smtp.password';
    const MAILER_SMTP_ENCRYPTION = 'mailer.smtp.encryption';
    const MAILER_SMTP_PORT = 'mailer.smtp.port';
    const MAILER_MAILGUN_DOMAIN = 'mailer.mailgun.domain';
    const MAILER_MAILGUN_KEY = 'mailer.mailgun.key';
    const MAILER_SPARKPOST_BASE_URL = 'mailer.sparkpost.base_url';
    const MAILER_SPARKPOST_API_KEY = 'mailer.sparkpost.api_key';

    //------- GUEST SignUp Mode -------//
    const GUEST_SIGNUP_MODE = 'guest.signup.mode';

    //------- One Signal -------//
    const ONE_SIGNAL_TAG_SYNC_ENABLED = 'one_signal.tag_sync.enabled';

    //------- BUKKU -------//
    const BUKKU_API_INVOICE_CACHE_ENABLED = 'bukku.api.invoice.cache.enabled';

    //------- IMAGE -------//
    const IMAGE_FIRESTORE_SYNC = 'image.firestore.sync';
    const IMAGE_GCP_PUB_SUB_TOPIC = 'image.gcp.pub.sub.topic';
    const IMAGE_THUMBNAIL_HANDLER = 'image.thumbnail.handler';
    const IMAGE_WATERMARK_HANDLER = 'image.watermark.handler';
    const IMAGE_METADATA_HANDLER = 'image.metadata.handler';

  //------- PUSH NOTIFICATION -------//
    const PUSH_NOTIFICATION_GCP_PUB_SUB_TOPIC = 'push_notification.gcp.pub.sub.topic';
    const PUSH_NOTIFICATION_COMMUNITY_HANDLER = 'push_notification.community.handler';
    const PUSH_NOTIFICATION_VCS_HANDLER = 'push_notification.vcs.handler';

    //------- PDF -------//
    const PDF_REPORT_HANDLER = 'pdf.report.handler';
    const PDF_SERVERLESS_URL = 'pdf.serverless.url';

    //------- SMS -------//
    const SMS_PROVIDER = 'sms.provider';
    const SMS_ISMS_USERNAME = 'sms.isms.username';
    const SMS_ISMS_PASSWORD = 'sms.isms.password';
    const SMS_ISMS_API_URL = 'sms.isms.api_url';
    const SMS_ISMS_BALANCE_API_URL = 'sms.isms.balance_api_url';

    const SMS_ONE_WAY_SMS_USERNAME = 'sms.one_way_sms.username';
    const SMS_ONE_WAY_SMS_PASSWORD = 'sms.one_way_sms.password';
    const SMS_ONE_WAY_SMS_API_URL = 'sms.one_way_sms.api_url';

    const SMS_HANDLER = 'sms.handler';
    const SMS_QUEUE_COMPONENT_ID = 'sms.queue.component.id';
    const SMS_GCP_PUB_SUB_TOPIC = 'sms.gcp.pub.sub.topic';

    const SMS_RESEND_TIME = 'sms.resend_time';


    //------- System Setting -------//
    const UPLOAD_IMAGE_MAXIMUM_SIZE = 'upload.image.max.size';
    const UPLOAD_DOCUMENT_MAXIMUM_SIZE = 'upload.document.max.size';

}
