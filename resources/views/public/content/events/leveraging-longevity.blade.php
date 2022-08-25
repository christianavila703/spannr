@extends('layouts.public')

@section('content')
    @include('public.content.events._header')
    <main class="page-spannr-event">
        <div class="container text-center py-5 text-dark-gray">
            <p class="lead-lg mb-2">Introducing...</p>
            <h1 class="title-large mb-4 text-tertiary">Lever<em>aging</em> Longevity</h1>
            <h2 class="h4 max-width-600 mx-auto mb-5 line-space-lg">
                Meet the entrepreneurs, investors, and scientists that are pushing the limits of human aging, and find out how you can get involved.
            </h2>
            @include('public.content.events._sd-banner')
            <div class="mx-auto max-width-700 text-start mt-5">
                <p>Spannr is pleased to announce that we'll be putting on our first-ever <strong>Lever<em>aging</em> Longevity event</strong> hosted in downtown San Diego, California on the evening of Friday, October 7, 2022.</p>

                <p>We've already cut the check, and the venue is officially booked. In other words, despite the short notice – it's going down.</p>

                <p>Join us on a special night to learn more about investment opportunities, cutting-edge science, and some of the new startups set to change the way humanity ages.</p>

                <p class="mb-1 h6">Here's all that you need to know...</p>
                <ul>
                    <li>
                        <strong>When:</strong> Friday, October 7, 2022
                    </li>
                    <li>
                        <strong>Where:</strong> <a href="https://www.sidebarsd.com/" target="_blank" rel="noopener noreferrer">Side Bar</a> 536 Market Street in San Diego, California
                    </li>
                    <li>
                        <strong>Schedule:</strong> Dinner + Speakers: 7pm – 9pm; Social: 9 pm – Late
                    </li>
                </ul>
                <p>While we're all pushing to live longer, healthier lives... who says we can't have some fun along the way?</p>

                <p>We're inviting everyone (that's you!) in hopes of bringing together the industry's top entrepreneurs, investors, and researchers pushing the limits on human aging.</p>

                <div class="text-center h6 text-quaternary">
                    sponsored by
                </div>
                <div class="logos-list">
                    <div class="logo-item">
                        <a href="https://www.explorerequity.com" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('images/logos/explorer-equity-group.png') }}" alt="Explorer Equity Group" />
                        </a>
                    </div>
                    <div class="logo-item">
                        <a href="https://codyshirk.com" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('images/logos/explorer-report.png') }}" alt="Explorer Report" />
                        </a>
                    </div>
                    <div class="logo-item">
                        <a href="https://age-rate.com" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('images/logos/age-rate.webp') }}" alt="age-rate.com" />
                        </a>
                    </div>
                    <div class="logo-item">
                        <a href="https://xivix.co/" target="_blank" rel="noopener noreferrer">
                            <img src="{{ asset('images/logos/xivix.png') }}" alt="xivix" />
                        </a>
                    </div>
                </div>

                <p>Stretching beyond a casual night of networking, we'll be providing dinner, drinks, gifts and a roundup of special guests.</p>

                <p>Our intentions are friendly, not fancy.</p>

                <p>Lever<em>aging</em> Longevity is an easy opportunity for anyone to get inside access to the fast moving, nascent world of longevity.</p>

                <p>The chance to meet with like-minded individuals and entrepreneurs all striving for the same goals of learning, networking, and advancing today's burgeoning aging industry is second to none.</p>

                <p>Tickets to the private, in-person event are currently going for $250. To book your seat at the table, click the link below.</p>

                <p>On behalf of the entire Spannr team, we're looking forward to meeting you there!</p>

                <div class="text-center">
                    <a href="https://buy.stripe.com/test_bIY7wx0rn4dBcKs7ss" class="btn btn-primary btn-xl btn-buy">Get Your Ticket</a>
                </div>
            </div>
        </div>
    </main>
@endsection
