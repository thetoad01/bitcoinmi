<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bitoin Michigan Network</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-secondary">
    @include('components.topnav')

    <div class="container">
        <div class="card card-body my-3">
            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <img src="\images\bitcoin-network-michigan-sm.png" alt="Bitcoin Michigan Network" class="img-fluid">
                </div>
                <div class="col-sm-12 col-md-9">
                    <div class="h3">Bitcoin: It's got some serious advantages.</div>
                    <div class="lead">
                        First off, it's digital, so you can send and receive money without all those traditional banking headaches and fees.
                        It's like global money on your terms. Plus, it's not controlled by any big authority, so it's like financial freedom.
                        Investing in Bitcoin can be a wild ride, but the potential for making some real cash is insane. It's way more interesting
                        than your typical investment options, and for someone looking to explore the world of finance, Bitcoin's got a lot to offer.
                    </div>
                    <div class="mt-4 lead">
                        @if ($data)
                            Current spot price on Coinbase:
                            <span class="fw-bold">${{ number_format($data['data']['amount'], 2) }} {{ $data['data']['currency'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-body mb-3">
            <div class="h3">Michigan:</div>
            <div class="lead">
                Michigan, often referred to as the Great Lakes State, is a diverse and vibrant state located in the Midwestern region of
                the United States. It's known for its iconic shape, surrounded by four of the five Great Lakes, which greatly influence
                its climate and provide stunning shorelines. The state is rich in natural beauty, boasting numerous forests, lakes,
                and outdoor recreational opportunities. From the bustling city of Detroit, a hub of industry and culture, to the serene
                wilderness of the Upper Peninsula, Michigan offers a wide range of experiences. The state is famous for its automotive
                heritage, being home to the "Motor City" and the American auto industry. Michigan also has a strong sports culture, with
                passionate fan bases for teams like the Detroit Lions, Tigers, and Red Wings. Whether you're into exploring nature,
                enjoying urban life, or soaking in the local culture, Michigan has something for everyone .... including a vibrant Bitcoin scene!
            </div>
        </div>
        
        <div class="card card-body mb-3">
            <div class="h3">.net</div>
            <div class="lead">
                <p>
                    The original intent of .net was to signify a connection to networking and technical aspects of the internet.
                    We use .net to signify that this is a connection to Michigan based networking for and about Bitcoin!
                </p>
                <p>
                    Networks of people serve as essential connectors, fostering collaboration, support, and interaction within a specific geographic area.
                    They help individuals and communities thrive by leveraging their shared interests, resources, and local knowledge.
                    Michigan offers many regional Bitcoin in-person meetups and events, providing the opportunity to meet fellow Bitcoin
                    enthusiasts face-to-face, share experiences, and have discussions.  We look forward to helping Michigan Bitcoiners get
                    insights into local Bitcoin meetups, events, businesses that accept Bitcoin, and relevant regulatory information.
                </p>
            </div>
        </div>
        
        <div class="card card-body mb-4">
            <div class="h3">Why Michigan Needs Bitcoin</div>
            <div class="lead">
                <p>
                    In today's rapidly evolving global landscape, Bitcoin stands as a beacon of financial sovereignty and empowerment. 
                    We live in a world where traditional financial systems are laden with inefficiencies, centralization, and opaque 
                    intermediaries. Bitcoin offers a revolutionary alternative, a decentralized digital currency that transcends borders 
                    and intermediaries. It empowers individuals by providing them with true ownership of their wealth, allowing them to 
                    transact peer-to-peer without the need for intermediaries or third-party trust. This is a fundamental shift in 
                    how we perceive and engage with money.
                </p>
                <p>
                    Bitcoin is not merely a currency; it's a technology that underpins a new era of financial inclusion. It's a tool for 
                    the unbanked and underbanked to access the global economy. It's a hedge against inflation and a store of value in 
                    an era of endless money printing. Moreover, it's a catalyst for innovation, fostering a new ecosystem of decentralized 
                    applications and financial services. Michigan and the world needs Bitcoin because it represents a paradigm shift away from the 
                    traditional financial system, enabling financial autonomy and redefining the very nature of money itself. It's 
                    not just an investment; it's a revolution.
                </p>
            </div>
        </div>
    </div>
</body>
</html>