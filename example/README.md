# Example for cheppers/otpsp-client

1. Run `ngrok http 1234`
1. Run `export OTPSP_MERCHANT_ID='TODO'`
1. Run `export OTPSP_SECRET_KEY='TODO'`
1. Run `export OTPSP_BASE_URL="$(curl --silent http://localhost:4040/api/tunnels | jq -r '.tunnels[] | select(.proto == "http") | .public_url')"`
1. Run `php -S 127.0.0.1:1234 -t ./docroot`
