<?php
class PageController extends Controller {

    /**
     * Hiển thị trang Giới thiệu.
     */
    public function about() {
        $data = ['title' => 'Về Chúng Tôi'];
        $this->loadView('pages/about', $data);
    }

    /**
     * Hiển thị trang Liên hệ.
     */
    public function contact() {
        $data = ['title' => 'Liên Hệ'];
        $this->loadView('pages/contact', $data);
    }

    /**
     * Hiển thị trang Chính sách bảo hành.
     */
    public function warranty() {
        $data = ['title' => 'Chính Sách Bảo Hành'];
        $this->loadView('pages/warranty', $data);
    }
}