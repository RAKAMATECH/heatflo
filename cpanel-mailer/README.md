# cPanel mail relay

`mailer.php` is a small HTTPS endpoint that the Render-hosted Laravel API
calls to send lead-notification emails. It exists because Render's free
plan blocks outbound SMTP, so the API cannot reach the cPanel SMTP server
on ports 25/465/587. HTTPS (port 443) is not blocked, so the API POSTs
the rendered email here and the cPanel host forwards it via the local
mail server.

## Deploying

1. Open `mailer.php` and replace `PASTE_THE_RANDOM_SECRET_HERE` with the
   shared secret. Use the same secret as `MAIL_RELAY_SECRET` on Render.
2. Adjust `ALLOWED_RECIPIENTS`, `FROM_ADDRESS`, `FROM_NAME` if needed.
3. Upload the edited `mailer.php` to the cPanel `public_html/` directory
   (or a subpath). The file must be reachable over HTTPS at the URL set
   in `MAIL_RELAY_URL` on Render.
4. Confirm with: `curl -i https://<host>/mailer.php` — expected response
   is `HTTP/1.1 405 method not allowed` (the endpoint only accepts POST).

## Security

- Bearer-token auth using `hash_equals` for constant-time comparison.
- Recipient whitelist prevents the endpoint from being used as an open
  relay even if the secret leaks.
- 64 KB body cap.
- No file logging — failures are reported in the JSON response, which the
  Laravel API logs server-side.

## Do not commit real secrets

The committed `mailer.php` contains a placeholder. Real secrets live only
on the running cPanel host and in Render's environment-variable store.
