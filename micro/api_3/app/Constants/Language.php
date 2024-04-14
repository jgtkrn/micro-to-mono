<?php

namespace App\Constants;

enum Language: string {
    case CANTONESE = '廣東話';
    case MANDARIN = '普通話';
    case ENGLISH = '英語';
    case HAKKA_DIALECT = '客家話';
    case TEOCHEW_DIALECT = '潮州話';
    case HOKKIEN_DIALECT = '福建話';
    case TAISHAN_DIALECT = '台山話';
    case KAIPING_DIALECT = '開平話';
    case NEW_CONVERSATION = '新會話';
    case THAI_LANGUAGE = '泰國語';
    case SHANGHAINESE = '上海話';
    case NINGBO_DIALECT = '寧波話';
    case OTHER = '其他';

    public function value(): string
    {
        return match ($this) {
            self::CANTONESE => '廣東話',
            self::MANDARIN => '普通話',
            self::ENGLISH => '英語',
            self::HAKKA_DIALECT => '客家話',
            self::TEOCHEW_DIALECT => '潮州話',
            self::HOKKIEN_DIALECT => '福建話',
            self::TAISHAN_DIALECT => '台山話',
            self::KAIPING_DIALECT => '開平話',
            self::NEW_CONVERSATION => '新會話',
            self::THAI_LANGUAGE => '泰國語',
            self::SHANGHAINESE => '上海話',
            self::NINGBO_DIALECT => '寧波話',
            self::OTHER => '其他',
        };
    }
}
