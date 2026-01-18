@extends('layouts.app')

@section('title', 'News')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h1 class="lead">Michigan Bitcoin in the News</h1>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Sterling Heights works on consumer protections for Bitcoin ATMs</div>
                        <div class="pb-2">Officials in Sterling Heights, Michigan, are proposing ordinance changes to regulate and strengthen consumer protections for virtual currency kiosks, including Bitcoin ATMs, to keep residents safer from fraud while retaining access to innovative payment tech.</div>
                        <div>
                            <a itemprop="url" href="https://www.govtech.com/security/michigan-city-eyes-security-ordinance-for-virtual-currency" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">GovTech</span>
                            <span itemprop="dateCreated" class="ps-2">Dec 2, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitcoin Depot pilots Bitcoin kiosks in Wild Bill's stores</div>
                        <div class="pb-2">Bitcoin Depot has started a retail partnership with Wild Bill's Tobacco — headquartered in Troy, Michigan — rolling out a pilot of 10 Bitcoin ATMs (BTMs) in Midwest stores with plans to expand into more than 250 locations. This initiative makes buying Bitcoin more accessible to customers locally and regionally.</div>
                        <div>
                            <a itemprop="url" href="https://www.globenewswire.com/news-release/2025/11/19/3190850/0/en/Bitcoin-Depot-Partners-with-Wild-Bill-s-Tobacco-for-Multi-Store-Pilot-Eyeing-Wider-Midwest-Expansion.html" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Globe Newswire</span>
                            <span itemprop="dateCreated" class="ps-2">Nov 19, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Cease-and-desist order issued to bitcoin mining site in Eaton County, Michigan</div>
                        <div class="pb-2">Township officials in Hamlin Township ordered a local Bitcoin mining trailer to shut down after neighbors complained about high-pitched noise. The dispute highlights rising Bitcoin mining activity in Michigan and zoning challenges for new facilities.</div>
                        <div>
                            <a itemprop="url" href="https://www.wkar.org/wkar-news/2025-11-19/cease-and-desist-issued-to-bitcoin-mining-site-in-eaton-county-over-noise-complaints" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">WKAR Public Media</span>
                            <span itemprop="dateCreated" class="ps-2">Nov 19, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Michigan can't afford to miss the crypto moment</div>
                        <div class="pb-2">A Detroit News opinion column arguing Michigan should actively embrace Bitcoin and blockchain innovation to remain competitive in technology, finance, and advanced manufacturing.</div>
                        <div>
                            <a itemprop="url" href="https://www.detroitnews.com/story/opinion/2025/10/26/manz-michigan-cant-afford-to-miss-the-crypto-moment/86839573007/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Detroit News</span>
                            <span itemprop="dateCreated" class="ps-2">Oct 26, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Hyperscale Data to mine Bitcoin, expand NVIDIA-powered AI data center in Michigan</div>
                        <div class="pb-2">Hyperscale Data announced upgrades to its Dowagiac, Michigan facility, combining Bitcoin mining with AI data-center expansion. The piece frames Bitcoin mining as part of a broader high-performance computing and infrastructure investment strategy.</div>
                        <div>
                            <a itemprop="url" href="https://www.dbusiness.com/hustle-and-muscle-articles/hyperscale-data-to-mine-bitcoin-expand-nvidia-ai-data-center-in-michigan/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">DBusiness</span>
                            <span itemprop="dateCreated" class="ps-2">Oct 6, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Michigan moves forward with strategic crypto reserve bill</div>
                        <div class="pb-2">Although not strictly local reporting, this article provides important background on Michigan lawmakers discussing whether public funds could allocate a portion to large-cap cryptocurrencies like Bitcoin.</div>
                        <div>
                            <a itemprop="url" href="https://cryptonews.com.au/news/michigan-moves-forward-with-strategic-crypto-reserve-bill-130935/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">CryptoNews</span>
                            <span itemprop="dateCreated" class="ps-2">Sept 22, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Michigan Bitcoin Trade Council helps take the mystery out of cryptocurrency</div>
                        <div class="pb-2">A new Michigan nonprofit, the Michigan Bitcoin Trade Council, launched to educate residents about Bitcoin, demystify key concepts like blockchain and cold storage, and encourage broader adoption and understanding of Bitcoin within the state. The organization offers resources, meet-ups, and support to help Michiganders engage with Bitcoin more confidently. This coverage focuses on education and opportunity rather than controversy.</div>
                        <div>
                            <a itemprop="url" href="https://www.fox2detroit.com/news/michigan-bitcoin-trade-council-helps-take-mystery-out-of-cryptocurrency" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">FOX 2 Detroit</span>
                            <span itemprop="dateCreated" class="ps-2">Aug 6, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Michigan State Pension Triples Bitcoin ETF Holdings</div>
                        <div class="pb-2">Michigan's State Retirement System significantly increased its exposure to Bitcoin by tripling its holdings in the ARK 21Shares Bitcoin ETF to approximately 300,000 shares in the second quarter of 2025. This move reflects institutional interest in regulated Bitcoin investment products and positions the state among broader U.S. public pension adoption trends.</div>
                        <div>
                            <a itemprop="url" href="https://bitbo.io/news/michigan-pension-bitcoin-etf/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Bitbo</span>
                            <span itemprop="dateCreated" class="ps-2">Aug 6, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitcoin rally hits Lansing as lawmakers weigh crypto for Michigan's state pensions</div>
                        <div class="pb-2">Enthusiasts gathered at the Capitol lawn in Lansing for a Bitcoin rally while Michigan lawmakers considered legislation aimed at integrating cryptocurrency more deeply into the state's economy, including potential use by state pension systems. The event highlighted growing interest in crypto adoption and policy discussion in Mid-Michigan. This coverage reports on the rally and legislative context without focusing on crime or complaints.</div>
                        <div>
                            <a itemprop="url" href="https://www.wkar.org/wkar-news/2025-07-31/bitcoin-rally-hits-lansing-as-lawmakers-weigh-crypto-for-michigans-state-pensions" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">WKAR Public Media</span>
                            <span itemprop="dateCreated" class="ps-2">July 31, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Crypto mining comes to Michigan. How does it work?</div>
                        <div class="pb-2">A Bridge Michigan explanatory article on cryptocurrency and how crypto mining, including Bitcoin mining, operates within the state. It outlines the basics of the technology, how mining functions, and the presence of several large mining operations in Michigan, offering context on the industry's technological basis and local relevance.</div>
                        <div>
                            <a itemprop="url" href="https://bridgemi.com/business-watch/crypto-mining-comes-michigan-how-does-it-work/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Bridge Michigan</span>
                            <span itemprop="dateCreated" class="ps-2">July 15, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">"Cryptocurrency Bill of Rights" introduced in Michigan House</div>
                        <div class="pb-2">Michigan Public reports on a bipartisan House package that would bar state/local governments from banning or taxing cryptocurrency differently than fiat, add guardrails for state crypto investments (including Bitcoin-related funds), and create a program allowing Bitcoin miners to bid to use abandoned state-owned oil and gas wells in exchange for remediation and potential tax breaks.</div>
                        <div>
                            <a itemprop="url" href="https://www.michiganpublic.org/politics-government/2025-05-28/cryptocurrency-bill-of-rights-introduced-in-michigan-house" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Michigan Public</span>
                            <span itemprop="dateCreated" class="ps-2">May 28, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">How Crypto is Changing the Sports Betting Landscape</div>
                        <div class="pb-2">A Michigan-focused tech outlet overview of how cryptocurrencies (including Bitcoin) are being used for deposits/withdrawals in sports betting, with a Michigan-specific reference to licensed sportsbook revenue as context for adoption in the state.</div>
                        <div>
                            <a itemprop="url" href="https://mitechnews.com/blockchain/how-crypto-is-changing-the-sports-betting-landscape/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">MiTechNews</span>
                            <span itemprop="dateCreated" class="ps-2">March 2, 2025</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">The Bitcoin Bounce: Cryptocurrency surges following election</div>
                        <div class="pb-2">A Lansing-based report explaining Bitcoin's post-election price surge (including crossing $100,000) and what it could mean for investors in Mid-Michigan. The story features local perspectives from LifePlan Financial Design president Mike Douglas and Wayne State University finance professor Tom Shohfi, emphasizing volatility, portfolio sizing, and learning the basics before investing.</div>
                        <div>
                            <a itemprop="url" href="https://www.wilx.com/2024/12/06/bitcoin-bounce-cryptocurrency-surges-following-election/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">WILX News 10</span>
                            <span itemprop="dateCreated" class="ps-2">Dec 6, 2024</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Ault Alliance to Begin Holding Bitcoin on Its Balance Sheet, Marking Change in Financial Strategy</div>
                        <div class="pb-2">LAS VEGAS, January 10, 2024--(BUSINESS WIRE)--Ault Alliance, Inc. (NYSE American: AULT), a diversified holding company 
                            ("Ault Alliance," or the "Company"), today announced that the Company has decided that it will start holding up to 20 percent and 
                            minimum of five percent of the Bitcoin it mines on the Company’s balance sheet. This strategic move is part of the Company’s broader 
                            plan to adjust its asset management approach to ultimately create a more valuable enterprise and drive stockholder value.
                        </div>
                        <div>... [December 2023] around 77 Bitcoin were mined at Sentinum’s data center in Michigan, while approximately 74 Bitcoin came from mining machines hosted with Core Scientific, Inc.</div>
                        <div>    
                            <a itemprop="url" href="https://finance.yahoo.com/news/ault-alliance-begin-holding-bitcoin-113000548.html" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Yahoo!Finance</span>
                            <span itemprop="dateCreated" class="ps-2">Jan 10, 2024</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Ault Alliance’s Subsidiary, Sentinum, Announces 133 Bitcoin Mined in November 2023 with a Current Annual Run Rate of $70.1 Million</div>
                        <div class="pb-2">LAS VEGAS, December 06, 2023--(BUSINESS WIRE)--Ault Alliance, Inc. (NYSE American: AULT), a diversified holding company
                            ("Ault Alliance," or the "Company"), announced today that its wholly owned subsidiary, Sentinum, Inc. ("Sentinum") mined approximately
                            133 Bitcoin in November 2023. Of this total, approximately 71 Bitcoin were mined at Sentinum’s data center in Michigan, with the remaining
                            approximately 62 Bitcoin from mining machines hosted with Core Scientific, Inc. Based on the current Bitcoin price of approximately $44,000,
                            Sentinum mined approximately $5.84 million worth of Bitcoin in November 2023 and has a current Bitcoin mining operations annual run rate of approximately $70.1 million worth of Bitcoin.</div>
                        <div>
                            <a itemprop="url" href="https://finance.yahoo.com/news/ault-alliance-subsidiary-sentinum-announces-113000401.html" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Yahoo!Finance</span>
                            <span itemprop="dateCreated" class="ps-2">Dec 6, 2023</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitcoin in Dentistry: The Future of Money</div>
                        <div class="pb-2">In an age when technological advancements are redefining every profession, dentistry isn’t untouched. Artificial intelligence (AI), digital radiography, teledentistry, and intraoral scanners are just a few examples. But beyond dentistry-specific technology, there’s a financial revolution that every dentist—in fact, every professional—should be aware of: Bitcoin.</div>
                        <div>
                            <a itemprop="url" href="https://www.dentaleconomics.com/money/article/14299765/bitcoin-in-dentistry-the-future-of-money" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Dental Economics</span>
                            <span itemprop="dateCreated" class="ps-2">Nov 30, 2023</span>
                        </div>
                        <div class="small">Written by your local Grand Rapids Bitcoin dentist Ryan!</div>
                    </li>
                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Southwest Michigan pizzeria piloting cryptocurrency payment option</div>
                        <div class="pb-2">COLON, MI -- Five Star Pizza is now accepting Bitcoin at its flagship location in Colon, Michigan.
                            Five Star Pizza is the first pizzeria in the region to accept Bitcoin Lightning payments, according to a press release from Detroit-based startup MI Lightning Rod.</div>
                        <div>
                            <a itemprop="url" href="https://www.mlive.com/news/kalamazoo/2023/10/southwest-michigan-pizzeria-piloting-cryptocurrency-payment-option.html" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Mlive</span>
                            <span itemprop="dateCreated" class="ps-2">Oct 28, 2023</span>
                        </div>
                    </li>
                    
                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitnile Announces Annualized Run Rate of 1,484 Bitcoin in Michigan</div>
                        <div class="pb-2">
                            BitNile Inc., a subsidiary of Las Vegas’ Ault Alliance Inc. with a Bitcoin mining operation in Dowagiac (southwest of Kalamazoo), announced its mining production is currently operating at an estimated annualized run rate of 1,484 Bitcoin based on current market conditions, including a mining difficulty of 34.09 trillion.
                        </div>
                        <div>
                            <a itemprop="url" href="https://www.dbusiness.com/daily-news/bitnile-announces-annualized-run-rate-of-1484-bitcoin-in-michigan/" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">DBusiness</span>
                            <span itemprop="dateCreated" class="ps-2">Jan 9, 2023</span>
                        </div>
                    </li>

                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitcoin Beach North and Building Out Bitcoin in a Bank Building</div>
                        <div class="pb-2">The Bitcoin takeover of Michigan has been strengthened by the addition of a new Bitcoin beach and education center.</div>
                        <div>
                            <a itemprop="url" href="https://bitcoinmagazine.com/culture/bitcoin-beach-north-and-bank-building" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Bitcoin Magazine</span>
                            <span itemprop="dateCreated" class="ps-2">Sept 7, 2022</span>
                        </div>
                    </li>
                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Progress of Bitcoin Mining in Michigan</div>
                        <div class="pb-2">When it comes to Bitcoin mining, Michigan is definitely one of the places to watch.
                            The state has seen a surge in interest in cryptocurrency mining, thanks to its cheap
                            electricity and cool climate. You can also use bitcode ai for further guidelines....</div>
                        <div>
                            <a itemprop="url" href="https://nativenewsonline.net/advertise/branded-voices/progress-of-bitcoin-mining-in-michigan" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">Native News Online</span>
                            <span itemprop="dateCreated" class="ps-2">May 5, 2022</span>
                        </div>
                    </li>
                    <li class="list-group-item" itemscope itemtype="http://schema.org/Article">
                        <div class="h3" itemprop="headline">Bitcoin Miner to ramp up power capacity to 300 MW at Michigan Data Center</div>
                        <div class="pb-2">Cryptocurrency mining requires intensive amounts of energy to complete transactions. The Michigan plant expansion is part of a proposed multi-year power agreement with a nuclear power facility and other zero-carbon power generation resources.</div>
                        <div>
                            <a itemprop="url" href="https://www.energytech.com/microgrids/article/21235455/bitcoin-miner-to-ramp-power-capacity-to-300-mw-at-michigan-data-center" target="_new" class="btn btn-sm btn-success">Read more</a>
                        </div>
                        <div class="small">
                            Soure:  <span itemprop="creator">EnergyTech</span>
                            <span itemprop="dateCreated" class="ps-2">March 7, 2022</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
