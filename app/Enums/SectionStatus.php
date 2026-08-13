<?php

namespace App\Enums;

enum SectionStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
    case Review = 'review';

    /**
     * Human label used across the CMS UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Published => 'Published',
            self::Draft => 'Draft',
            self::Review => 'In Review',
        };
    }

    /**
     * Tailwind classes for the status pill, mirroring the CMS design's badge colours.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Published => 'bg-cms-green-soft text-cms-green',
            self::Draft => 'bg-cms-border text-cms-text-2',
            self::Review => 'bg-cms-amber-soft text-cms-amber',
        };
    }
}
