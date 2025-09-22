<?php

namespace App\Constants;

class Status
{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    const TICKET_OPEN = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY = 2;
    const TICKET_CLOSE = 3;

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING = 2;
    const KYC_VERIFIED = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const JOB_PENDING   = 0;
    const JOB_APPROVED  = 1;
    const JOB_COMPLETED = 2;
    const JOB_PAUSE     = 3;
    const JOB_ONGOING     = 4;
    const JOB_REJECTED  = 9;

    const JOB_PROVE_PENDING = 0;
    const JOB_PROVE_APPROVE = 1;
    const JOB_PROVE_REJECT  = 2;
    const JOB_PROVE_START   = 3;
    const JOB_PROVE_CANCEL  = 4;
    const JOB_PROVE_COMPLETE = 5;
    const JOB_PROVE_COMPLETE_CONFIRM = 6;

    const JOB_PROVE_REQUIRED = 2;
    const JOB_PROVE_OPTIONAL = 1;

    const ORDER_PENDING = 0;
    const ORDER_PROCESSING = 1;
    const ORDER_COMPLETED = 2;
    const ORDER_CANCELED = 3;

    const DELIVERY_PENDING = 0;
    const DELIVERY_COMPLETE = 1;
    const DELIVERY_CANCELED = 2;

    const PROMOTION_BANNER_ENABLE = 0;
    const PROMOTION_BANNER_DISABLE = 1;

    const ADMIN_ACTIVE = 1;
    const ADMIN_BAN = 0;

    const SUB_CATEGORIES_ENABLE = 1;
    const SUB_CATEGORIES_DISABLE = 0;

    const CATEGORIES_ENABLE = 1;
    const CATEGORIES_DISABLE = 0;

    const SUB_CATEGORIES_CONDITION_ENABLE = 1;
    const SUB_CATEGORIES_CONDITION_DISABLE = 0;

    // advertisement section
    // const ADVERTISEMENT_ENABLE = 1;
    // const ADVERTISEMENT_DISABLE = 0;

    const ADVERTISEMENT_BOOSTED = 1;
    const ADVERTISEMENT_NOT_BOOSTED = 0;

    const AD_PENDING   = 0;
    const AD_APPROVED  = 1;
    const AD_COMPLETED = 2;
    const AD_PAUSE     = 3;
    const AD_ONGOING     = 4;
    const AD_EXPIRED   = 5;
    const AD_CANCELED  = 6;
    const AD_REJECTED  = 9;

    const PAID_AD = 0;
    const FREE_AD = 1;

    // advertisement package
    const TYPE_WEEKLY = 1;
    const TYPE_MONTHLY = 2;

    const PACKAGE_ACTIVE = 1;
    const PACKAGE_INACTIVE = 0;

    const FREE_PACKAGE = 0;
    const BASIC_PACKAGE = 1;
    const PREMIUM_PACKAGE = 2;
    const ENTERPRISE_PACKAGE = 3;

    // activation expired
    const ACTIVATION_EXPIRED = 1;
    const ACTIVATION_NOT_EXPIRED = 0;

    // advertisement package & package history
    const BOOST_PACKAGE_AVAILABLE = 1;
    const BOOST_PACKAGE_NOT_AVAILABLE = 0;

    // sub categories
    const CONDITION_SUPPORT = 1;
    const CONDITION_NOT_SUPPORT = 0;

    // payment options
    const FREE = 0;
    const PAY_BY_WALLET = 1;

    // boost package
    const BOOST_PACKAGE_ACTIVE = 1;
    const BOOST_PACKAGE_INACTIVE = 0;

    const TOP_PACKAGE = 1;
    const FEATURED_PACKAGE = 2;
    const URGENT_PACKAGE = 3;

    const HIGH_PRIORITY = 1;
    const MEDIUM_PRIORITY = 2;
    const LOW_PRIORITY = 3;

    const HIGHLIGHTED_COLOR_ACTIVE = 1;
    const HIGHLIGHTED_COLOR_INACTIVE = 0;

    // boost histories
    const PACKAGE_BOOST = 1;
    const CASH_BOOST = 0;

    // can affect to the both advertisement section and advertisement boosted history
    const BOOST_NOT_STARTED = 0;
    const BOOST_STARTED = 1;
    const BOOST_COMPLETED = 2;
    // const BOOST_EXPIRED = 3;
  
    // users
    const CUSTOMER = 1;
    const LEADER = 2;

    // users is top leader
    const NORMAL_LEADER = 0;
    const TOP_LEADER = 1;

    // customer bonus 
    const VOUCHER_OPEN = 1;
    const VOUCHER_CLOSED = 0;

    // account type
    const PRO_ACCOUNT = 1;
    const LITE_ACCOUNT = 0;

    // training
    const TRAINING_PENDING = 1;
    const TRAINING_COMPLETED = 2;
    const TRAINING_REJECTED = 3; 

    // claimed rank requirements
    const RANK_PENDING = 0;
    const RANK_ACHIEVED = 1;
    const RANK_NOT_SATISFIED = 0;
    const RANK_CLAIM_PENDING = 1;
    const RANK_CLAIM_PROCESSING = 2; // after user claimed the rank rewards
    const RANK_CLAIM_COMPLETED = 3;
    const RANK_CLAIM_CANCELED = 4;
}
