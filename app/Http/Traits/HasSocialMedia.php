<?php

namespace App\Http\Traits;

trait HasSocialMedia {
    public function getFacebookLinkAttribute(): string
    {
        return '<a href="https://www.facebook.com/' . $this->facebook . '" target="_blank" rel="noopener noreferrer">' . $this->facebook . '</a>';
    }

    public function getTwitterLinkAttribute(): string
    {
        return '<a href="https://www.twitter.com/' . $this->twitter . '" target="_blank" rel="noopener noreferrer">' . $this->twitter . '</a>';
    }

    public function getLinkedinLinkAttribute(): string
    {
        return '<a href="https://www.linkedin.com/' . $this->linkedin . '" target="_blank" rel="noopener noreferrer">' . $this->linkedin . '</a>';
    }

    public function getInstagramLinkAttribute(): string
    {
        return '<a href="https://www.instagram.com/' . $this->instagram . '" target="_blank" rel="noopener noreferrer">' . $this->instagram . '</a>';
    }

    public function getGoogleScholarLinkAttribute(): string
    {
        return '<a href="https://scholar.google.com/citations?user=' . $this->google_scholar . '" target="_blank" rel="noopener noreferrer">' . $this->google_scholar . '</a>';
    }

    public function getWebsiteLinkAttribute(): string
    {
        return '<a href="' . $this->website . '" target="_blank" rel="noopener noreferrer">' . $this->website . '</a>';
    }

    public function getFacebookUrlAttribute(): string|null
    {
        return $this->facebook ? 'https://www.facebook.com/' . $this->facebook : null;
    }

    public function getTwitterUrlAttribute(): string|null
    {
        return $this->twitter ? 'https://www.twitter.com/' . $this->twitter : null;
    }

    public function getLinkedinUrlAttribute(): string|null
    {
        return $this->linkedin ? 'https://www.linkedin.com/' . $this->linkedin : null;
    }

    public function getInstagramUrlAttribute(): string|null
    {
        return $this->instagram ? 'https://www.instagram.com/' . $this->instagram: null;
    }
}
