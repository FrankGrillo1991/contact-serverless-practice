# Contact Serverless • Practice

This is a minimal practice project to deploy a contact form to **Vercel** using a **serverless function**.

## Files
- `index.html` — Simple contact form posting to `/api/contact`.
- `api/contact.js` — Serverless function that validates fields and logs messages.
- `vercel.json` — Node.js 20 runtime and routes (SPA fallback to `index.html`).

## Local Setup
```bash
npm i -g vercel
vercel login
vercel link        # follow prompts
vercel dev         # http://localhost:3000
```

## Deploy
```bash
vercel            # preview
vercel --prod     # production
```

## Optional: Email Delivery
Add env vars in Vercel Project Settings → Environment Variables:
- `RESEND_API_KEY`
- `CONTACT_TO` (e.g., your email)

Uncomment the Resend section in `api/contact.js`, then redeploy.

## Verify
- Submit the form on your deployed site.
- Check logs in Vercel Dashboard → Deployments → Logs (or CLI: `vercel logs <url> --since=10m`).

---

© Practice template for learning purposes.
