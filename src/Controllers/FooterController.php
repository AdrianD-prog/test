<?php

    namespace App\Controllers;

use App\Enums\RequestType;

/**
 * Kontroler obsługujący stopkę strony.
 */
class FooterController extends Controller
{
    public function aboutUs(): void
    {

        $this->view->renderPage('footer/about-us', [
            'title' => 'O nas',

            'user' => $this->user,
            'cart' => $this->cart,

            'scripts' => [],
            'styles' => []
        ]);
    }

    public function careers(): void
    {
        $offers = $this->api->request(RequestType::GET, '/careers');

        $this->view->renderPage('footer/careers', [
            'title' => 'Kariera',

            'user' => $this->user,
            'cart' => $this->cart,
            'offers' => $offers ?? [],

        ]);
    }

    public function investorRelations(): void
    {
        // Partnerzy logistyczni
        $partners = [
            [
                'name' => 'InPost',
                'logo' => '/public/static/images/delivery_companies/inpost.png',
                'description' => 'Lider w dostawach paczkomatowych w Polsce. Szybkie i wygodne doręczenia 24/7.',
                'since' => '2020',
                'benefits' => ['Paczkomaty 24/7', 'Szybka dostawa', 'Śledzenie przesyłek']
            ],
            [
                'name' => 'DPD',
                'logo' => '/public/static/images/delivery_companies/dpd.svg',
                'description' => 'Największy operator pocztowy w Polsce, zapewniający niezawodne dostawy na terenie całego kraju.',
                'since' => '2021',
                'benefits' => ['Zasięg ogólnopolski', 'Dostawy do domu', 'Wygodne opłaty']
            ],
            [
                'name' => 'UPS',
                'logo' => '/public/static/images/delivery_companies/ups.jpg',
                'description' => 'Globalny lider w logistyce i dostawach międzynarodowych.',
                'since' => '2022',
                'benefits' => ['Dostawy międzynarodowe', 'Ekspresowa wysyłka', 'Monitoring GPS']
            ],
            [
                'name' => 'GLS',
                'logo' => '/public/static/images/delivery_companies/gls.png',
                'description' => 'Europejska sieć kurierska z naciskiem na jakość i terminowość dostaw.',
                'since' => '2021',
                'benefits' => ['Dostawy do 24h', 'Pakowanie pod wymiar', 'Wygodne zwroty']
            ]
        ];

        $stats = [
            ['value' => '500k+', 'label' => 'Przesyłek rocznie'],
            ['value' => '4', 'label' => 'Partnerów logistycznych'],
            ['value' => '98%', 'label' => 'Dostaw na czas'],
            ['value' => '24/7', 'label' => 'Wsparcie']
        ];

        $this->view->renderPage('footer/investor-relations', [
            'title' => 'Relacje inwestorskie',
            'user' => $this->user,
            'cart' => $this->cart,
            'partners' => $partners,
            'stats' => $stats,
        ]);
    }

    public function affiliate(): void
    {
        $levels = [
            [
                'name' => 'Starter',
                'commission' => '5%',
                'requirements' => 'Brak wymagań',
                'color' => 'primary',
                'features' => ['Linki afiliacyjne', 'Raporty dzienne', 'Wsparcie email']
            ],
            [
                'name' => 'Pro',
                'commission' => '8%',
                'requirements' => '10+ sprzedaży/miesiąc',
                'color' => 'success',
                'features' => ['Wszystko ze Starter', 'Dedykowany opiekun', 'Priorytetowe wsparcie', 'Bonus startowy 200zł']
            ],
            [
                'name' => 'Premium',
                'commission' => '12%',
                'requirements' => '50+ sprzedaży/miesiąc',
                'color' => 'danger',
                'features' => ['Wszystko z Pro', 'Indywidualne stawki', 'Wsparcie 24/7', 'Darmowe produkty do recenzji', 'Udział w programie beta']
            ]
        ];

        $faqs = [
            ['q' => 'Czym jest program afiliacyjny?', 'a' => 'To program partnerski, w którym zarabiasz prowizję za polecanie naszych produktów znajomym lub na swoich stronach.'],
            ['q' => 'Kiedy otrzymuję wypłatę?', 'a' => 'Wypłaty są realizowane co miesiąc, po przekroczeniu progu 100 zł. Przelewy realizujemy do 15 dnia miesiąca.'],
            ['q' => 'Jak mogę promować?', 'a' => 'Poprzez linki afiliacyjne na blogu, w social media, na YouTube lub stronie internetowej.'],
            ['q' => 'Czy są jakieś ukryte koszty?', 'a' => 'Nie! Program jest całkowicie darmowy. Zaczynasz zarabiać od pierwszej sprzedaży.']
        ];

        $this->view->renderPage('footer/affiliate', [
            'title' => 'Program afiliacyjny',
            'user' => $this->user,
            'cart' => $this->cart,
            'levels' => $levels,
            'faqs' => $faqs,
        ]);
    }

    public function help(): void
    {
        $this->view->renderPage('footer/help', [
            'title' => 'Centrum pomocy',
            'user' => $this->user,
            'cart' => $this->cart,
        ]);
    }
    public function returns(): void
    {
        $this->view->renderPage('footer/returns', [
            'title' => 'Zwroty i reklamacje',
            'user' => $this->user,
            'cart' => $this->cart,
        ]);
    }
    public function contact(): void
    {
        $this->view->renderPage('footer/contact', [
            'title' => 'Kontakt',
            'user' => $this->user,
            'cart' => $this->cart,
        ]);
    }
}
