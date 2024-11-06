<?php

const CSV_SEPARATOR = ["Sep=;"];
const ENTITY_SUPPLY = 'supply';
const ENTITY_REFUND_ITEM = 'refund_item';
const ENTITY_REFUND = 'refund';
const ENTITY_CART = 'cart';
const RECENTLY_VIEWED = 'recent_history';
const ENTITY_PAYROLL = 'payroll';
const ENTITY_COMMISSION = 'commission';
const ENTITY_PURCHASE_ITEM = 'purchase_item';
const ENTITY_PURCHASE = 'purchase';
const ENTITY_CLIENT = 'client';
const ENTITY_CONTACT = 'contact';
const ENTITY_INVOICE = 'invoice';
const ENTITY_PROFORMA = 'proforma';
const ENTITY_ORDER = 'order';
const ENTITY_COMPLEMENT = 'complement';
const ENTITY_PROVIDER_INVOICE = 'provider_invoice';
const ENTITY_DOCUMENT = 'document';
const ENTITY_INVOICE_ITEM = 'invoice_item';
const ENTITY_STORE_CREDIT = 'store_credit';
const ENTITY_STORE_CREDIT_PAYMENT = 'store_credit_payment';
const ENTITY_MONEY_INCOME = 'money_income';
const ENTITY_MONEY_OUTCOME = 'money_outcome';
const ENTITY_MONEY_TRANSFER = 'money_transfer';
const ENTITY_INVITATION = 'invitation';
const ENTITY_RECURRING_INVOICE = 'recurring_invoice';
const ENTITY_PAYMENT = 'payment';
const ENTITY_DEPOSIT = 'abono';
const ENTITY_CREDIT = 'credit';
const ENTITY_QUOTE = 'quote';
const ENTITY_TASK = 'task';
const ENTITY_JOURNAL_ENTRY = 'journal_entry';
const ENTITY_ACCOUNTS_RECEIVABLE = 'accounts_receivable';
const ENTITY_MOST_SELLED_PRODUCTS = 'most_selled_products';
const ENTITY_CLIENT_POINTS = 'client_points';
const ENTITY_CLIENT_VISITS = 'client_visits';
const ENTITY_LESS_SELLED_PRODUCTS = 'less_selled_products';
const ENTITY_MOST_REQUESTED_PRODUCTS = 'most_requested_products';
const ENTITY_PRODUCT_REVENUE = 'product_revenue';
const ENTITY_SALES_BY_SELLER = 'sales_by_seller';
const ENTITY_SALES_BY_CLIENT = 'sales_by_client';
const ENTITY_SALE_ITEMS_GROUPED = 'sale_items_grouped';
const ENTITY_INVENTORY_MOVEMENTS = 'inventory_movements';
const ENTITY_TRANSFER_ITEMS_RECEIVED = 'transfer_items_received';
const ENTITY_ITEMS_BY_CLIENT = 'items_by_client';
const ENTITY_TRANSFER = 'transfer';
const ENTITY_CURRICULUM = 'curriculum';
const ENTITY_TRANSFER_HISTORY = 'transfer_history';
const ENTITY_TRANSFER_ITEMS_HISTORY = 'transfer_items_history';
const ENTITY_TRANSFER_ITEMS_GROUPED = 'transfer_items_grouped';
const ENTITY_INVENTORY_ENTRIES = 'inventory_entries';
const ENTITY_INVENTORY_MANUAL_UPDATE = 'inventory_manual_update';
const ENTITY_SALES = 'sales';
const ENTITY_PROFIT = 'profit';
const ENTITY_INCOME_EXPENSES = 'income_expenses';
const ENTITY_ACCOUNT_GATEWAY = 'account_gateway';
const ENTITY_USER = 'user';
const ENTITY_TOKEN = 'token';
const ENTITY_TAX_RATE = 'tax_rate';
const ENTITY_PRODUCT = 'product';
const ENTITY_DAMAGE_PRODUCT = 'damage_product';
const ENTITY_MISSING_PRODUCT = 'missing_product';
const ENTITY_PRODUCT_ALERT = 'products_alert';
const ENTITY_BRAND = 'brand';
const ENTITY_EMPLOYEE = 'employee';
const ENTITY_MECHANIC = 'mechanic';
const ENTITY_CATEGORY = 'category';
const ENTITY_ACTIVITY = 'activity';
const ENTITY_VENDOR = 'vendor';
const ENTITY_VENDOR_ACTIVITY = 'vendor_activity';
const ENTITY_EXPENSE = 'expense';
const ENTITY_PAYMENT_TERM = 'payment_term';
const ENTITY_EXPENSE_ACTIVITY = 'expense_activity';
const ENTITY_BANK_ACCOUNT = 'bank_account';
const ENTITY_BANK_SUBACCOUNT = 'bank_subaccount';
const ENTITY_EXPENSE_CATEGORY = 'expense_category';
const ENTITY_INCOME_CATEGORY = 'income_category';
const ENTITY_REFERRER = 'referrer';
const ENTITY_REFERRAL = 'referral';
const ENTITY_ORDER_REQUEST = 'order_request';
const ENTITY_PRODUCT_REQUEST = 'product_request';
const ENTITY_ORDER_REQUEST_CONFIRMED = 'order_request_confirmed';
const ENTITY_PRODUCT_REQUEST_CONFIRMED = 'product_request_confirmed';
const ENTITY_QUOTA = 'quota';
const ENTITY_REFUND_QUOTA = 'refund_quota';
const ENTITY_PAYMENT_QUOTA = 'payment_quota';
const ENTITY_SPECIAL_NEGOTIATION = 'special_negotiation';

const INVOICE_TYPE_STANDARD = 1;
const INVOICE_TYPE_QUOTE = 2;
const INVOICE_TYPE_PROFORMA = 3;
const INVOICE_TYPE_ORDER = 4;
const INVOICE_TYPE_COMPLEMENT = 5;
//tiendas que no dan promocion ni puntos
const STORES_WITHOUT_VOUCHERS = [6, 19];
const PERSON_CONTACT = 'contact';
const PERSON_USER = 'user';
const PERSON_VENDOR_CONTACT = 'vendorcontact';
const FINANCE_TYPE_INCOME = 1;
const FINANCE_TYPE_EXPENSE = 2;
const FINANCE_TYPE_TRANSFER = 3;
const FINANCE_ACCOUNT = 'finance_account';
const FINANCE_TRANSACTION = 'finance_transaction';
const FINANCE_INCOME = 'finance_income';
const FINANCE_EXPENSE = 'finance_expense';
const BASIC_SETTINGS = 'basic_settings';
const ADVANCED_SETTINGS = 'advanced_settings';

const ACCOUNT_COMPANY_DETAILS = 'company_details';
const ACCOUNT_USER_DETAILS = 'user_details';
// new code
const ACCOUNT_BILLING = 'billing';
const ACCOUNT_REFUND = 'refund';
const ACCOUNT_LOCALIZATION = 'localization';
const ACCOUNT_NOTIFICATIONS = 'notifications';
const ACCOUNT_IMPORT_EXPORT = 'import_export';
const ACCOUNT_MANAGEMENT = 'account_management';
const ACCOUNT_PAYMENTS = 'online_payments';
const ACCOUNT_BANKS = 'bank_accounts';
const ACCOUNT_IMPORT_EXPENSES = 'import_expenses';
const ACCOUNT_MAP = 'import_map';
const ACCOUNT_EXPORT = 'export';
const ACCOUNT_TAX_RATES = 'tax_rates';
const ACCOUNT_PRODUCTS = 'products';
const ACCOUNT_ADVANCED_SETTINGS = 'advanced_settings';
const ACCOUNT_INVOICE_SETTINGS = 'invoice_settings';
const ACCOUNT_INVOICE_DESIGN = 'invoice_design';
const ACCOUNT_CLIENT_PORTAL = 'client_portal';
const ACCOUNT_EMAIL_SETTINGS = 'email_settings';
const ACCOUNT_REPORTS = 'reports';
const ACCOUNT_USER_MANAGEMENT = 'user_management';
const ACCOUNT_DATA_VISUALIZATIONS = 'data_visualizations';
const ACCOUNT_TEMPLATES_AND_REMINDERS = 'templates_and_reminders';
const ACCOUNT_API_TOKENS = 'api_tokens';
const ACCOUNT_CUSTOMIZE_DESIGN = 'customize_design';
const ACCOUNT_SYSTEM_SETTINGS = 'system_settings';
const ACCOUNT_PAYMENT_TERMS = 'payment_terms';
const ACCOUNT_AREAS_AND_ZONES = 'areas_and_zones';
const ACCOUNT_ORGANIZATION_COMPANY = 'organization_company';
const ACCOUNT_SET_TOKEN = 'set_token';

const ACTION_RESTORE = 'restore';
const ACTION_ARCHIVE = 'archive';
const ACTION_CLONE = 'clone';
const ACTION_CONVERT = 'convert';
const ACTION_DELETE = 'delete';

const ACTIVITY_TYPE_CREATE_CLIENT = 1;
const ACTIVITY_TYPE_ARCHIVE_CLIENT = 2;
const ACTIVITY_TYPE_DELETE_CLIENT = 3;
const ACTIVITY_TYPE_CREATE_INVOICE = 4;
const ACTIVITY_TYPE_UPDATE_INVOICE = 5;
const ACTIVITY_TYPE_EMAIL_INVOICE = 6;
const ACTIVITY_TYPE_VIEW_INVOICE = 7;
const ACTIVITY_TYPE_ARCHIVE_INVOICE = 8;
const ACTIVITY_TYPE_DELETE_INVOICE = 9;
const ACTIVITY_TYPE_CREATE_PAYMENT = 10;
//define('ACTIVITY_TYPE_UPDATE_PAYMENT', 11);
const ACTIVITY_TYPE_ARCHIVE_PAYMENT = 12;
const ACTIVITY_TYPE_DELETE_PAYMENT = 13;
const ACTIVITY_TYPE_CREATE_CREDIT = 14;
//define('ACTIVITY_TYPE_UPDATE_CREDIT', 15);
const ACTIVITY_TYPE_ARCHIVE_CREDIT = 16;
const ACTIVITY_TYPE_DELETE_CREDIT = 17;
const ACTIVITY_TYPE_CREATE_QUOTE = 18;
const ACTIVITY_TYPE_UPDATE_QUOTE = 19;
const ACTIVITY_TYPE_EMAIL_QUOTE = 20;
const ACTIVITY_TYPE_VIEW_QUOTE = 21;
const ACTIVITY_TYPE_ARCHIVE_QUOTE = 22;
const ACTIVITY_TYPE_DELETE_QUOTE = 23;
const ACTIVITY_TYPE_RESTORE_QUOTE = 24;
const ACTIVITY_TYPE_RESTORE_INVOICE = 25;
const ACTIVITY_TYPE_RESTORE_CLIENT = 26;
const ACTIVITY_TYPE_RESTORE_PAYMENT = 27;
const ACTIVITY_TYPE_RESTORE_CREDIT = 28;
const ACTIVITY_TYPE_APPROVE_QUOTE = 29;
const ACTIVITY_TYPE_CREATE_VENDOR = 30;
const ACTIVITY_TYPE_ARCHIVE_VENDOR = 31;
const ACTIVITY_TYPE_DELETE_VENDOR = 32;
const ACTIVITY_TYPE_RESTORE_VENDOR = 33;
const ACTIVITY_TYPE_CREATE_EXPENSE = 34;
const ACTIVITY_TYPE_ARCHIVE_EXPENSE = 35;
const ACTIVITY_TYPE_DELETE_EXPENSE = 36;
const ACTIVITY_TYPE_RESTORE_EXPENSE = 37;
const ACTIVITY_TYPE_VOIDED_PAYMENT = 39;
const ACTIVITY_TYPE_REFUNDED_PAYMENT = 40;
const ACTIVITY_TYPE_FAILED_PAYMENT = 41;
const ACTIVITY_TYPE_CREATE_TASK = 42;
const ACTIVITY_TYPE_UPDATE_TASK = 43;
const ACTIVITY_TYPE_ARCHIVE_TASK = 44;
const ACTIVITY_TYPE_DELETE_TASK = 45;
const ACTIVITY_TYPE_RESTORE_TASK = 46;
const ACTIVITY_TYPE_UPDATE_EXPENSE = 47;


const DEFAULT_INVOICE_NUMBER = '0001';
const RECENTLY_VIEWED_LIMIT = 20;
const LOGGED_ERROR_LIMIT = 100;
const RANDOM_KEY_LENGTH = 32;
const MAX_NUM_USERS = 20;
const MAX_IMPORT_ROWS = 800;
const MAX_SUBDOMAIN_LENGTH = 30;
const MAX_IFRAME_URL_LENGTH = 250;
const MAX_LOGO_FILE_SIZE = 200; // KB
const MAX_FAILED_LOGINS = 10;
define('MAX_INVOICE_ITEMS', env('MAX_INVOICE_ITEMS', 100));
define('MAX_DOCUMENT_SIZE', env('MAX_DOCUMENT_SIZE', 10000));// KB
define('MAX_EMAIL_DOCUMENTS_SIZE', env('MAX_EMAIL_DOCUMENTS_SIZE', 10000));// Total KB
define('MAX_ZIP_DOCUMENTS_SIZE', env('MAX_EMAIL_DOCUMENTS_SIZE', 30000));// Total KB (uncompressed)
define('DOCUMENT_PREVIEW_SIZE', env('DOCUMENT_PREVIEW_SIZE', 300));// pixels
const DEFAULT_FONT_SIZE = 9;
const DEFAULT_HEADER_FONT = 1;// Roboto
const DEFAULT_BODY_FONT = 1;// Roboto
const DEFAULT_SEND_RECURRING_HOUR = 8;

const IMPORT_CSV = 'CSV';
const IMPORT_JSON = 'JSON';
const IMPORT_FRESHBOOKS = 'FreshBooks';
const IMPORT_WAVE = 'Wave';
const IMPORT_RONIN = 'Ronin';
const IMPORT_HIVEAGE = 'Hiveage';
const IMPORT_ZOHO = 'Zoho';
const IMPORT_NUTCACHE = 'Nutcache';
const IMPORT_INVOICEABLE = 'Invoiceable';
const IMPORT_HARVEST = 'Harvest';

const MAX_NUM_CLIENTS = 100;
const MAX_NUM_CLIENTS_PRO = 20000;
const MAX_NUM_CLIENTS_LEGACY = 500;
const MAX_INVOICE_AMOUNT = 1000000000;
const LEGACY_CUTOFF = 57800;
const ERROR_DELAY = 3;

const MAX_NUM_VENDORS = 100;
const MAX_NUM_VENDORS_PRO = 20000;

const INVOICE_STATUS_DRAFT = 1;
const INVOICE_STATUS_SENT = 2;
const INVOICE_STATUS_VIEWED = 3;
const INVOICE_STATUS_APPROVED = 4;
const INVOICE_STATUS_PARTIAL = 5;
const INVOICE_STATUS_PAID = 6;

const SUPPLY_STATUS_DRAFT = 1;
const SUPPLY_STATUS_SENT = 2;
const SUPPLY_STATUS_VIEWED = 3;
const SUPPLY_STATUS_APPROVED = 4;
const SUPPLY_STATUS_PARTIAL = 5;
const SUPPLY_STATUS_PAID = 6;

const PAYMENT_STATUS_PENDING = 1;
const PAYMENT_STATUS_VOIDED = 2;
const PAYMENT_STATUS_FAILED = 3;
const PAYMENT_STATUS_COMPLETED = 4;
const PAYMENT_STATUS_PARTIALLY_REFUNDED = 5;
const PAYMENT_STATUS_REFUNDED = 6;

const CUSTOM_DESIGN = 11;

const FREQUENCY_WEEKLY = 1;
const FREQUENCY_TWO_WEEKS = 2;
const FREQUENCY_FOUR_WEEKS = 3;
const FREQUENCY_MONTHLY = 4;
const FREQUENCY_THREE_MONTHS = 5;
const FREQUENCY_SIX_MONTHS = 6;
const FREQUENCY_ANNUALLY = 7;

const SESSION_TIMEZONE = 'timezone';
const SESSION_CURRENCY = 'currency';
const SESSION_CURRENCY_DECORATOR = 'currency_decorator';
const SESSION_DATE_FORMAT = 'dateFormat';
const SESSION_DATE_PICKER_FORMAT = 'datePickerFormat';
const SESSION_DATETIME_FORMAT = 'datetimeFormat';
const SESSION_COUNTER = 'sessionCounter';
const SESSION_LOCALE = 'sessionLocale';
const SESSION_USER_ACCOUNTS = 'userAccounts';
const SESSION_REFERRAL_CODE = 'referralCode';
const SESSION_LEFT_SIDEBAR = 'showLeftSidebar';
const SESSION_RIGHT_SIDEBAR = 'showRightSidebar';

const SESSION_LAST_REQUEST_PAGE = 'SESSION_LAST_REQUEST_PAGE';
const SESSION_LAST_REQUEST_TIME = 'SESSION_LAST_REQUEST_TIME';

const SESSION_CURRENT_USER_AUTH = 'current_user_auth';
const SESSION_CURRENT_REAL_USER_AUTH = 'current_real_user_auth';
const SESSION_YESTERDAY_CASH_COUNT_PASS = 'yesterday_cash_count_pass';
const SESSION_CASHCOUNT_PASS = 'cashcount_pass';

const CURRENCY_DOLLAR = 1;
const CURRENCY_EURO = 3;

const DEFAULT_TIMEZONE = 'US/Eastern';
const DEFAULT_COUNTRY = 840; // United Stated
const DEFAULT_CURRENCY = CURRENCY_DOLLAR;
const DEFAULT_LANGUAGE = 1; // English
const DEFAULT_DATE_FORMAT = 'M j, Y';
const DEFAULT_DATE_PICKER_FORMAT = 'M d, yyyy';
const DEFAULT_DATETIME_FORMAT = 'F j, Y g:i a';
const DEFAULT_DATETIME_MOMENT_FORMAT = 'MMM D, YYYY h:mm:ss a';
const DEFAULT_LOCALE = 'en';
const DEFAULT_MAP_ZOOM = 10;

const RESULT_SUCCESS = 'success';
const RESULT_FAILURE = 'failure';


const PAYMENT_LIBRARY_OMNIPAY = 1;
const PAYMENT_LIBRARY_PHP_PAYMENTS = 2;

const GATEWAY_AUTHORIZE_NET = 1;
const GATEWAY_EWAY = 4;
const GATEWAY_MOLLIE = 9;
const GATEWAY_PAYFAST = 13;
const GATEWAY_PAYPAL_EXPRESS = 17;
const GATEWAY_PAYPAL_PRO = 18;
const GATEWAY_SAGE_PAY_DIRECT = 20;
const GATEWAY_SAGE_PAY_SERVER = 21;
const GATEWAY_STRIPE = 23;
const GATEWAY_GOCARDLESS = 6;
const GATEWAY_TWO_CHECKOUT = 27;
const GATEWAY_BEANSTREAM = 29;
const GATEWAY_PSIGATE = 30;
const GATEWAY_MOOLAH = 31;
const GATEWAY_BITPAY = 42;
const GATEWAY_DWOLLA = 43;
const GATEWAY_CHECKOUT_COM = 47;
const GATEWAY_CYBERSOURCE = 49;
const GATEWAY_WEPAY = 60;
const GATEWAY_BRAINTREE = 61;
const GATEWAY_CUSTOM = 62;

// The customer exists, but only as a local concept
// The remote gateway doesn't understand the concept of customers
const CUSTOMER_REFERENCE_LOCAL = 'local';

const EVENT_CREATE_CLIENT = 1;
const EVENT_CREATE_INVOICE = 2;
const EVENT_CREATE_QUOTE = 3;
const EVENT_CREATE_PAYMENT = 4;
const EVENT_CREATE_VENDOR = 5;

const REQUESTED_PRO_PLAN = 'REQUESTED_PRO_PLAN';
const DEMO_ACCOUNT_ID = 'DEMO_ACCOUNT_ID';
const PREV_USER_ID = 'PREV_USER_ID';
const NINJA_ACCOUNT_KEY = 'zg4ylmzDkdkPOT8yoKQw9LTWaoZJx79h';
const NINJA_GATEWAY_ID = GATEWAY_STRIPE;
const NINJA_GATEWAY_CONFIG = 'NINJA_GATEWAY_CONFIG';

define('PDFMAKE_DOCS', env('PDFMAKE_DOCS', 'http://pdfmake.org/playground.html'));
define('PHANTOMJS_CLOUD', env('PHANTOMJS_CLOUD', 'http://api.phantomjscloud.com/api/browser/v2/'));
define('PHP_DATE_FORMATS', env('PHP_DATE_FORMATS', 'http://php.net/manual/en/function.date.php'));
define('REFERRAL_PROGRAM_URL', env('REFERRAL_PROGRAM_URL', 'https://www.invoiceninja.com/referral-program/'));
define('EMAIL_MARKUP_URL', env('EMAIL_MARKUP_URL', 'https://developers.google.com/gmail/markup'));
define('OFX_HOME_URL', env('OFX_HOME_URL', 'http://www.ofxhome.com/index.php/home/directory/all'));
define('GOOGLE_ANALYITCS_URL', env('GOOGLE_ANALYITCS_URL', 'https://www.google-analytics.com/collect'));
define('TRANSIFEX_URL', env('TRANSIFEX_URL', 'https://www.transifex.com/invoice-ninja/invoice-ninja'));

const MSBOT_LOGIN_URL = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
const MSBOT_LUIS_URL = 'https://api.projectoxford.ai/luis/v1/application';
const SKYPE_API_URL = 'https://apis.skype.com/v3';
const MSBOT_STATE_URL = 'https://state.botframework.com/v3';

const BLANK_IMAGE = 'data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';

const COUNT_FREE_DESIGNS = 4;
const COUNT_FREE_DESIGNS_SELF_HOST = 5; // include the custom design
const PRODUCT_ONE_CLICK_INSTALL = 1;
const PRODUCT_INVOICE_DESIGNS = 2;
const PRODUCT_WHITE_LABEL = 3;
const PRODUCT_SELF_HOST = 4;
const WHITE_LABEL_AFFILIATE_KEY = '92D2J5';
const INVOICE_DESIGNS_AFFILIATE_KEY = 'T3RS74';
const SELF_HOST_AFFILIATE_KEY = '8S69AD';

define('WHITE_LABEL_PRICE', env('WHITE_LABEL_PRICE', 20));
define('INVOICE_DESIGNS_PRICE', env('INVOICE_DESIGNS_PRICE', 10));

const USER_TYPE_SELF_HOST = 'SELF_HOST';
const USER_TYPE_CLOUD_HOST = 'CLOUD_HOST';
const NEW_VERSION_AVAILABLE = 'NEW_VERSION_AVAILABLE';

const DEFAULT_API_PAGE_SIZE = 15;
const MAX_API_PAGE_SIZE = 500;

define('IOS_PUSH_CERTIFICATE', env('IOS_PUSH_CERTIFICATE', ''));

const TOKEN_BILLING_DISABLED = 1;
const TOKEN_BILLING_OPT_IN = 2;
const TOKEN_BILLING_OPT_OUT = 3;
const TOKEN_BILLING_ALWAYS = 4;

const PAYMENT_TYPE_CREDIT = 1;
const PAYMENT_TYPE_ACH = 5;
const PAYMENT_TYPE_VISA = 6;
const PAYMENT_TYPE_MASTERCARD = 7;
const PAYMENT_TYPE_AMERICAN_EXPRESS = 8;
const PAYMENT_TYPE_DISCOVER = 9;
const PAYMENT_TYPE_DINERS = 10;
const PAYMENT_TYPE_EUROCARD = 11;
const PAYMENT_TYPE_NOVA = 12;
const PAYMENT_TYPE_CREDIT_CARD_OTHER = 13;
const PAYMENT_TYPE_PAYPAL = 14;
const PAYMENT_TYPE_CARTE_BLANCHE = 17;
const PAYMENT_TYPE_UNIONPAY = 18;
const PAYMENT_TYPE_JCB = 19;
const PAYMENT_TYPE_LASER = 20;
const PAYMENT_TYPE_MAESTRO = 21;
const PAYMENT_TYPE_SOLO = 22;
const PAYMENT_TYPE_SWITCH = 23;

const PAYMENT_METHOD_STATUS_NEW = 'new';
const PAYMENT_METHOD_STATUS_VERIFICATION_FAILED = 'verification_failed';
const PAYMENT_METHOD_STATUS_VERIFIED = 'verified';

const GATEWAY_TYPE_CREDIT_CARD = 1;
const GATEWAY_TYPE_BANK_TRANSFER = 2;
const GATEWAY_TYPE_PAYPAL = 3;
const GATEWAY_TYPE_BITCOIN = 4;
const GATEWAY_TYPE_DWOLLA = 5;
const GATEWAY_TYPE_CUSTOM = 6;
const GATEWAY_TYPE_TOKEN = 'token';

const REMINDER1 = 'reminder1';
const REMINDER2 = 'reminder2';
const REMINDER3 = 'reminder3';

const REMINDER_DIRECTION_AFTER = 1;
const REMINDER_DIRECTION_BEFORE = 2;

const REMINDER_FIELD_DUE_DATE = 1;
const REMINDER_FIELD_INVOICE_DATE = 2;

const FILTER_INVOICE_DATE = 'invoice_date';
const FILTER_PAYMENT_DATE = 'payment_date';

const SOCIAL_GOOGLE = 'Google';
const SOCIAL_FACEBOOK = 'Facebook';
const SOCIAL_GITHUB = 'GitHub';
const SOCIAL_LINKEDIN = 'LinkedIn';

const USER_STATE_ACTIVE = 'active';
const USER_STATE_PENDING = 'pending';
const USER_STATE_DISABLED = 'disabled';
const USER_STATE_ADMIN = 'admin';
const USER_STATE_OWNER = 'owner';

const API_SERIALIZER_ARRAY = 'array';
const API_SERIALIZER_JSON = 'json';

const EMAIL_DESIGN_PLAIN = 1;
const EMAIL_DESIGN_LIGHT = 2;
const EMAIL_DESIGN_DARK = 3;

const BANK_LIBRARY_OFX = 1;

const CURRENCY_DECORATOR_CODE = 'code';
const CURRENCY_DECORATOR_SYMBOL = 'symbol';
const CURRENCY_DECORATOR_NONE = 'none';

const RESELLER_REVENUE_SHARE = 'A';
const RESELLER_LIMITED_USERS = 'B';

const AUTO_BILL_OFF = 1;
const AUTO_BILL_OPT_IN = 2;
const AUTO_BILL_OPT_OUT = 3;
const AUTO_BILL_ALWAYS = 4;

// Pro
const FEATURE_CUSTOMIZE_INVOICE_DESIGN = 'customize_invoice_design';
const FEATURE_REMOVE_CREATED_BY = 'remove_created_by';
const FEATURE_DIFFERENT_DESIGNS = 'different_designs';
const FEATURE_EMAIL_TEMPLATES_REMINDERS = 'email_templates_reminders';
const FEATURE_INVOICE_SETTINGS = 'invoice_settings';
const FEATURE_CUSTOM_EMAILS = 'custom_emails';
const FEATURE_PDF_ATTACHMENT = 'pdf_attachment';
const FEATURE_MORE_INVOICE_DESIGNS = 'more_invoice_designs';
const FEATURE_QUOTES = 'quotes';
const FEATURE_TASKS = 'tasks';
const FEATURE_EXPENSES = 'expenses';
const FEATURE_REPORTS = 'reports';
const FEATURE_BUY_NOW_BUTTONS = 'buy_now_buttons';
const FEATURE_API = 'api';
const FEATURE_CLIENT_PORTAL_PASSWORD = 'client_portal_password';
const FEATURE_CUSTOM_URL = 'custom_url';

const FEATURE_MORE_CLIENTS = 'more_clients'; // No trial allowed

// Whitelabel
const FEATURE_CLIENT_PORTAL_CSS = 'client_portal_css';
const FEATURE_WHITE_LABEL = 'feature_white_label';

// Enterprise
const FEATURE_DOCUMENTS = 'documents';

// No Trial allowed
const FEATURE_USERS = 'users';// Grandfathered for old Pro users
const FEATURE_USER_PERMISSIONS = 'user_permissions';

// Pro users who started paying on or before this date will be able to manage users
const PRO_USERS_GRANDFATHER_DEADLINE = '2016-06-04';
const EXTRAS_GRANDFATHER_COMPANY_ID = 35089;

// WePay
const WEPAY_PRODUCTION = 'production';
const WEPAY_STAGE = 'stage';
define('WEPAY_CLIENT_ID', env('WEPAY_CLIENT_ID'));
define('WEPAY_CLIENT_SECRET', env('WEPAY_CLIENT_SECRET'));
define('WEPAY_AUTO_UPDATE', env('WEPAY_AUTO_UPDATE', false));
define('WEPAY_ENVIRONMENT', env('WEPAY_ENVIRONMENT', WEPAY_PRODUCTION));
define('WEPAY_ENABLE_CANADA', env('WEPAY_ENABLE_CANADA', false));
define('WEPAY_THEME', env('WEPAY_THEME','{"name":"Invoice Ninja","primary_color":"0b4d78","secondary_color":"0b4d78","background_color":"f8f8f8","button_color":"33b753"}'));

const SKYPE_CARD_RECEIPT = 'message/card.receipt';
const SKYPE_CARD_CAROUSEL = 'message/card.carousel';
const SKYPE_CARD_HERO = '';

const BOT_STATE_GET_EMAIL = 'get_email';
const BOT_STATE_GET_CODE = 'get_code';
const BOT_STATE_READY = 'ready';
const SIMILAR_MIN_THRESHOLD = 50;

// https://docs.botframework.com/en-us/csharp/builder/sdkreference/attachments.html
const SKYPE_BUTTON_OPEN_URL = 'openUrl';
const SKYPE_BUTTON_IM_BACK = 'imBack';
const SKYPE_BUTTON_POST_BACK = 'postBack';
const SKYPE_BUTTON_CALL = 'call'; // "tel:123123123123"
const SKYPE_BUTTON_PLAY_AUDIO = 'playAudio';
const SKYPE_BUTTON_PLAY_VIDEO = 'playVideo';
const SKYPE_BUTTON_SHOW_IMAGE = 'showImage';
const SKYPE_BUTTON_DOWNLOAD_FILE = 'downloadFile';

const INVOICE_FIELDS_CLIENT = 'client_fields';
const INVOICE_FIELDS_INVOICE = 'invoice_fields';
const INVOICE_FIELDS_ACCOUNT = 'account_fields';

$creditCards = [
    1 => ['card' => 'images/credit_cards/Test-Visa-Icon.png', 'text' => 'Visa'],
    2 => ['card' => 'images/credit_cards/Test-MasterCard-Icon.png', 'text' => 'Master Card'],
    4 => ['card' => 'images/credit_cards/Test-AmericanExpress-Icon.png', 'text' => 'American Express'],
    8 => ['card' => 'images/credit_cards/Test-Diners-Icon.png', 'text' => 'Diners'],
    16 => ['card' => 'images/credit_cards/Test-Discover-Icon.png', 'text' => 'Discover']
];
define('CREDIT_CARDS', serialize($creditCards));

$cachedTables = [
    'currencies' => 'App\Models\Currency',
    'sizes' => 'App\Models\Size',
    'timezones' => 'App\Models\Timezone',
    'dateFormats' => 'App\Models\DateFormat',
    'datetimeFormats' => 'App\Models\DatetimeFormat',
    'languages' => 'App\Models\Language',
    'paymentTerms' => 'App\Models\PaymentTerm',
    'paymentTypes' => 'App\Models\PaymentType',
];
define('CACHED_TABLES', serialize($cachedTables));
