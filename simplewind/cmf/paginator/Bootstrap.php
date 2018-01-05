<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace cmf\paginator;

use think\Paginator;

class Bootstrap extends Paginator
{
    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );
        return '<a class="btn btn-default pull-left" href="'. htmlentities($url) .'" role="button">'.$text.'</a>';
        // return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '下一页')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);
        return '<a class="btn btn-default pull-left" href="'. htmlentities($url) .'" role="button">'.$text.'</a>';
        // return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getSimplePreviousButton($text = "上一页")
    {

        // if ($this->currentPage() <= 1) {
        //     return '<li class="disabled previous"><span>' . $text . '</span></li>';
        // }

        $url = $this->url(
            $this->currentPage() - 1
        );
        return '<a class="btn btn-default pull-left" href="'. htmlentities($url) .'" role="button">'.$text.'</a>';
        // return '<li class="previous"><a href="' . htmlentities($url) . '">' . $text . '</a></li>';
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getSimpleNextButton($text = '下一页')
    {
        // if (!$this->hasMore) {
        //     return '<li class="disabled next"><span>' . $text . '</span></li>';
        // }

        $url = $this->url($this->currentPage() + 1);
        return '<a class="btn btn-default pull-left" href="'. htmlentities($url) .'" role="button">'.$text.'</a>';
        // return '<li class="next"><a href="' . htmlentities($url) . '">' . $text . '</a></li>';
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple)
            return '';

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 2;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            // $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            // $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 0), $this->lastPage);
        } else {
            // $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            // $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }
        $html .= "";
        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            $request = request();

            if ($this->simple || $request->isMobile()) {
                return sprintf(
                    '%s %s',
                    $this->getSimplePreviousButton(),
                    $this->getSimpleNextButton()
                );
            } else {
                return sprintf(
                    '%s %s %s',
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<li><a href="' . htmlentities($url) . '">第&nbsp;' . $page . '&nbsp;页</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        // $html = '<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        // $html .= '第&nbsp;1&nbsp;页';
        // $html .= '<span class="caret"></span>';
        // $html .= '</button>';
        $html = '<a class="btn btn-default pull-left" href="javascript:;" role="button">'.$text.'</a>';
        return $html;
        // return '<li class="disabled"><span>' . $text . '</span></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        $html = '<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .= '第&nbsp;'.$text.'&nbsp;页';
        $html .= '<span class="caret"></span>';
        $html .= '</button>';
        return $html;
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        // return $this->getDisabledTextWrapper('');
        return "";
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
        $currentHtml = '';
        foreach ($urls as $page => $url) {
            if($page == $this->currentPage()) {
                $currentHtml.=$this->getActivePageWrapper($page);
            }else {
                $html .= $this->getPageLinkWrapper($url, $page);
            }
        }
        $html.="</ul>";
        $containerHtml = '<div class="dropup pull-left">'.$currentHtml.$html.'</div>';

        return $containerHtml;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        $html = $this->getAvailablePageWrapper($url, $page);
        // $html = '<div class="dropup pull-left">';
        // if ($page == $this->currentPage()) {
        //     $html.=$this->getActivePageWrapper($page);
        // }
        // if ($page != $this->currentPage()) {
        //     // $html.='<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">';
        //     $html.=$this->getAvailablePageWrapper($url, $page);
        //     // $html.='</ul></div>';
        // }
        
        return $html;
    }


}
