<?php

/**
 * @copyright Copyright (c) Hustle Hero
 */

namespace common\base\enum;

/**
 * Class DatabaseTable
 * @package common\base\enum
 */
class DatabaseTable extends BaseEnum
{
    const SYSTEM_CONFIG = '{{%system_config}}';
    const SYSTEM_ENUM = '{{%system_enum}}';
    const SYSTEM_QUEUE = '{{%system_queue}}';
    const HTTP_SESSION = '{{%http_session}}';
    const MODEL_CONFIG = '{{%model_config}}';
    const MODEL_TRANSLATION = '{{%model_translation}}';
    const MODEL_CACHE = '{{%model_cache}}';
    const TOKEN_DATA = '{{%token_data}}';

    const DEEPLINK = '{{%deeplink}}';
    const CATEGORY = '{{%category}}';

    const AUDIT_ENTRY = '{{%audit_entry}}';
    const AUDIT_ERROR = '{{%audit_error}}';
    const AUDIT_TRAIL = '{{%audit_trail}}';

    const PAGE_CONTENT = '{{%page_content}}';
    const FAQ = '{{%faq}}';
    const POST = '{{%post}}';

    const IMAGE = '{{%image}}';
    const IMAGE_CACHE = '{{%image_cache}}';

    const DOCUMENT = '{{%document}}';

    const CURRENCY = '{{%currency}}';
    const CURRENCY_RATE = '{{%currency_rate}}';

    const COUNTRY = '{{%country}}';
    const STATE = '{{%state}}';
    const CITY = '{{%city}}';
    const ADDRESS = '{{%address}}';
    const CONTACT = '{{%contact}}';

    const LANGUAGE = '{{%language}}';

    const LEAD = '{{%lead}}';
    const ACCOUNT = '{{%account}}';
    const ACCOUNT_USER = '{{%account_user}}';
    const SUBSCRIPTION = '{{%subscription}}';


    const SEARCH_SUGGESTION = '{{%search_suggestion}}';

    const ACCOUNT_SUBSCRIPTION = '{{%account_subscription}}';

    const BANK = '{{%bank}}';
    const BANK_ACCOUNT = '{{%bank_account}}';

    const USER = '{{%user}}';
    const USER_EMAIL = '{{%user_email}}';
    const USER_PHONE = '{{%user_phone}}';
    const USER_IDENTITY = '{{%user_identity}}';
    const USER_SOCIAL = '{{%user_social}}';

    const SMS_REQUEST = '{{%sms_request}}';
    const VERIFICATION_REQUEST = '{{%verification_request}}';

    const WALLET = '{{%wallet}}';
    const WALLET_TRANSACTION = '{{%wallet_transaction}}';
    const SETTLEMENT = '{{%settlement}}';

    const MODEL_ACTION = '{{%model_action}}';
}
