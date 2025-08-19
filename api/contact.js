// /api/contact.js — Serverless function for Vercel
// Endpoint: POST /api/contact
export default async function handler(req, res) {
  try {
    if (req.method !== 'POST') {
      return res.status(405).json({ ok: false, error: 'Method not allowed' });
    }

    const { name, email, message } = req.body || {};
    if (!name || !email || !message) {
      return res.status(400).json({ ok: false, error: 'Missing fields' });
    }

    // Basic validation
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    if (!emailOk) {
      return res.status(400).json({ ok: false, error: 'Invalid email' });
    }

    // Log to Vercel function logs for verification
    console.log('New contact:', { name, email, message });

    // OPTIONAL: Email provider integration (e.g., Resend)
    if (process.env.RESEND_API_KEY) {
      await fetch('https://api.resend.com/emails', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${process.env.RESEND_API_KEY}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          from: 'noreply@yourdomain.com',
          to: process.env.CONTACT_TO || 'you@example.com',
          subject: `New contact from ${name}`,
          html: `<p><b>Name:</b> ${name}<br/><b>Email:</b> ${email}<br/><b>Message:</b> ${message}</p>`
        })
      });
    }

    return res.status(200).json({ ok: true });
  } catch (err) {
    console.error('Function error:', err);
    return res.status(500).json({ ok: false, error: 'Server error' });
  }
}
