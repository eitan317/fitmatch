@if(config('services.adsense.verification_code'))
<!-- Google AdSense Site Verification -->
<meta name="google-adsense-account" content="{{ config('services.adsense.verification_code') }}">
@endif
