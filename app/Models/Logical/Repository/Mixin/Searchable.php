<?php
namespace App\Models\Logical\Repository\Mixin;

trait Searchable
{
    /**
     * @return array
     */
    public function getSeoKeywords()
    {
        return (array) $this->seo_keywords;
    }

    /**
     * @return static
     */
    public function setSeoKeywords($keywords)
    {
        $this->seo_keywords = (array) $keywords;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeoDescription()
    {
        return strval($this->seo_description);
    }

    /**
     * @return static
     */
    public function setSeoDescription($text)
    {
        $this->seo_description = $this->sanitize($text);

        return $this;
    }
}