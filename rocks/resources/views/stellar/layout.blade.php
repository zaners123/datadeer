<!DOCTYPE HTML>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"l>
	<head>
		<title>@yield("title")</title>
		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
        @yield('head')
	</head>
	<body class="is-preload">
		<!-- Wrapper -->
        <div id="wrapper">
            <!-- Header -->
            <header id="header">
                @yield('header')
            </header>
            <!-- Main -->
            <div id="main">
                @yield("main")
                <!-- Content -->
                <section id="content" class="main">
                    @yield("content")
                </section>
            </div>
            <!-- Footer -->
            <footer id="footer">
                @yield("footer")
                <section>
                    <h2>Aliquam sed mauris</h2>
                    <p>Sed lorem ipsum dolor sit amet et nullam consequat feugiat consequat magna adipiscing tempus etiam dolore veroeros. eget dapibus mauris. Cras aliquet, nisl ut viverra sollicitudin, ligula erat egestas velit, vitae tincidunt odio.</p>
                    <ul class="actions">
                        <li><a href="#" class="button">Learn More</a></li>
                    </ul>
                </section>
                <section>
                    <h2>Etiam feugiat</h2>
                    <dl class="alt">
                        <dt>Address</dt>
                        <dd>1234 Somewhere Road &bull; Nashville, TN 00000 &bull; USA</dd>
                        <dt>Phone</dt>
                        <dd>(000) 000-0000 x 0000</dd>
                        <dt>Email</dt>
                        <dd><a href="#">information@untitled.tld</a></dd>
                    </dl>
                    <x-social_media/>
                </section>
                <p class="copyright">&copy; Untitled. Design: <a href="https://html5up.net">HTML5 UP</a>.</p>
            </footer>
        </div>
        <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery.scrollex.min.js"></script>
        <script src="assets/js/jquery.scrolly.min.js"></script>
        <script src="assets/js/browser.min.js"></script>
        <script src="assets/js/breakpoints.min.js"></script>
        <script src="assets/js/util.js"></script>
        <script src="assets/js/main.js"></script>
	</body>
</html>
