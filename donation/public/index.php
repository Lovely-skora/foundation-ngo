<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

secureSession();
setSecurityHeaders();
createTables();
$csrf = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate — <?= APP_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="page-wrap">
  <div class="form-shell">

    <!-- Header -->
    <div class="form-header">
      <div class="org-logo">❤️</div>
      <h1><?= APP_NAME ?></h1>
      <p>Your donation changes lives</p>
    </div>

    <!-- Progress bar -->
    <div class="progress-bar" id="progress">
      <div class="step active" data-step="1"><span class="dot">1</span><span class="lbl">Details</span></div>
      <div class="step" data-step="2"><span class="dot">2</span><span class="lbl">Amount</span></div>
      <div class="step" data-step="3"><span class="dot">3</span><span class="lbl">Payment</span></div>
    </div>

    <!-- Alert box -->
    <div id="alert" class="alert hidden"></div>

    <!-- ───── STEP 1: Donor Info ───── -->
    <div class="form-step active" id="step1">
      <p class="step-title">Your details</p>
      <div class="field">
        <label>Full Name <span class="req">*</span></label>
        <input type="text" id="fname" placeholder="Rahul Sharma" maxlength="120" autocomplete="name">
      </div>
      <div class="row2">
        <div class="field">
          <label>Email <span class="req">*</span></label>
          <input type="email" id="femail" placeholder="rahul@email.com" autocomplete="email">
        </div>
        <div class="field">
          <label>Mobile <span class="req">*</span></label>
          <input type="tel" id="fphone" placeholder="+91 9876543210" autocomplete="tel">
        </div>
      </div>
      <div class="row2">
        <div class="field">
          <label>City</label>
          <input type="text" id="fcity" placeholder="Delhi" maxlength="80">
        </div>
        <div class="field">
          <label>State</label>
          <select id="fstate">
            <option value="">Select state</option>
            <?php
            $states = ['Andhra Pradesh','Assam','Bihar','Chhattisgarh','Delhi','Goa',
                       'Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
                       'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya',
                       'Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim',
                       'Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand',
                       'West Bengal','Other'];
            foreach ($states as $s) echo "<option>$s</option>\n";
            ?>
          </select>
        </div>
      </div>
      <div class="field">
        <label>PAN Number <span class="opt">(for 80G tax receipt)</span></label>
        <input type="text" id="fpan" placeholder="Optional" maxlength="10" style="text-transform:uppercase">
      </div>
      <button class="btn-primary" onclick="goToStep2()">Continue →</button>
      <div class="trust-badges">
        <span>🔒 SSL Secured</span>
        <span>🏛️ 80G Certified NGO</span>
        <span>✅ Razorpay Verified</span>
      </div>
    </div>

    <!-- ───── STEP 2: Amount ───── -->
    <div class="form-step" id="step2">
      <p class="step-title">Choose amount &amp; campaign</p>

      <div class="dtype-row">
        <button class="dtype-btn active" id="dt-once" onclick="setDtype('one-time')">⚡ One-time</button>
        <button class="dtype-btn" id="dt-monthly" onclick="setDtype('monthly')">🔄 Monthly</button>
      </div>

      <div class="amount-grid">
        <button class="amt-btn" onclick="pickAmt(this,'500')">₹500</button>
        <button class="amt-btn active" onclick="pickAmt(this,'1000')">₹1,000</button>
        <button class="amt-btn" onclick="pickAmt(this,'2500')">₹2,500</button>
        <button class="amt-btn" onclick="pickAmt(this,'5000')">₹5,000</button>
      </div>
      <div class="field">
        <input type="number" id="custom-amt" placeholder="Or enter custom amount (₹)" min="1" max="1000000" oninput="clearPreset()">
      </div>

      <div class="field">
        <label>Campaign</label>
        <select id="fcampaign">
          <option value="General Fund">General Fund</option>
          <option value="Feed Children">🍱 Feed Children</option>
          <option value="Plant Trees">🌱 Plant Trees</option>
          <option value="Education for All">📚 Education for All</option>
          <option value="Clean Water">💧 Clean Water</option>
        </select>
      </div>
      <div class="field">
        <label>Dedication message <span class="opt">(optional)</span></label>
        <textarea id="fmsg" placeholder="In memory of… / On behalf of…" rows="2" maxlength="300"></textarea>
      </div>
      <label class="check-row">
        <input type="checkbox" id="chk80g" checked>
        <span>I want an 80G tax receipt emailed to me</span>
      </label>

      <div class="btn-row">
        <button class="btn-back" onclick="showStep(1)">← Back</button>
        <button class="btn-primary" onclick="goToPayment()">Continue to Payment →</button>
      </div>
    </div>

    <!-- ───── STEP 3: Review & Pay ───── -->
    <div class="form-step" id="step3">
      <p class="step-title">Review &amp; pay</p>
      <div class="summary-box" id="summary"></div>
      <button class="btn-primary pay-btn" id="payBtn" onclick="startPayment()">
        🔒 Donate Securely
      </button>
      <p class="rzp-note">Powered by Razorpay · PCI-DSS Level 1 · UPI / Cards / Net Banking / Wallets</p>
      <div class="btn-row" style="margin-top:12px">
        <button class="btn-back" onclick="showStep(2)">← Back</button>
      </div>
    </div>

    <!-- ───── SUCCESS ───── -->
    <div class="form-step" id="stepSuccess">
      <div class="success-wrap">
        <div class="success-icon">❤️</div>
        <h2 id="suc-heading">Thank you!</h2>
        <p id="suc-msg"></p>
        <div class="txn-summary" id="suc-details"></div>
        <p class="suc-note">An email receipt has been sent to you.</p>
      </div>
    </div>

  </div><!-- /form-shell -->
</div><!-- /page-wrap -->

<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
const CSRF    = <?= json_encode($csrf) ?>;
const RZP_KEY = <?= json_encode(RAZORPAY_KEY_ID) ?>;
const BASE_URL = '<?= rtrim(APP_URL, '/') ?>/public';

let selAmt   = 1000;
let selDtype = 'one-time';

function showAlert(msg, type = 'error') {
  const el = document.getElementById('alert');
  el.textContent = msg;
  el.className = 'alert ' + type;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideAlert() { document.getElementById('alert').className = 'alert hidden'; }

function showStep(n) {
  document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
  document.getElementById('step' + n)?.classList.add('active') || document.getElementById('stepSuccess').classList.add('active');

  document.querySelectorAll('#progress .step').forEach(s => {
    const sn = parseInt(s.dataset.step);
    s.classList.remove('active', 'done');
    if (sn < n) s.classList.add('done');
    else if (sn === n) s.classList.add('active');
  });
  hideAlert();
  window.scrollTo(0, 0);
}

function goToStep2() {
  const name  = document.getElementById('fname').value.trim();
  const email = document.getElementById('femail').value.trim();
  const phone = document.getElementById('fphone').value.trim();
  if (!name)  return showAlert('Please enter your full name.');
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return showAlert('Please enter a valid email.');
  if (!phone || phone.replace(/\D/g,'').length < 10) return showAlert('Please enter a valid 10-digit mobile number.');
  showStep(2);
}

function setDtype(t) {
  selDtype = t;
  document.getElementById('dt-once').classList.toggle('active', t === 'one-time');
  document.getElementById('dt-monthly').classList.toggle('active', t === 'monthly');
}

function pickAmt(btn, v) {
  document.querySelectorAll('.amt-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selAmt = parseFloat(v);
  document.getElementById('custom-amt').value = '';
}

function clearPreset() {
  document.querySelectorAll('.amt-btn').forEach(b => b.classList.remove('active'));
}

function goToPayment() {
  const custom = document.getElementById('custom-amt').value;
  if (custom) selAmt = parseFloat(custom);
  if (!selAmt || selAmt < 1) return showAlert('Please enter a valid donation amount (minimum ₹1).');
  if (selAmt > 1000000) return showAlert('Maximum donation amount is ₹10,00,000.');

  const name     = document.getElementById('fname').value.trim();
  const campaign = document.getElementById('fcampaign').value;
  const dtype    = selDtype === 'one-time' ? 'One-time' : 'Monthly recurring';
  document.getElementById('summary').innerHTML = `
    <div class="sum-row"><span>Donor</span><strong>${escHtml(name)}</strong></div>
    <div class="sum-row"><span>Campaign</span><strong>${escHtml(campaign)}</strong></div>
    <div class="sum-row"><span>Type</span><strong>${dtype}</strong></div>
    <div class="sum-row total"><span>Total</span><strong>₹${selAmt.toLocaleString('en-IN')}</strong></div>
  `;
  showStep(3);
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function startPayment() {
  const btn = document.getElementById('payBtn');
  btn.disabled = true;
  btn.textContent = 'Creating order…';
  hideAlert();

  try {
    const res = await fetch(BASE_URL + '/create_order.php', {
      method: 'POST',
      // FIX 9: credentials: 'same-origin' ensures session cookie is sent with fetch
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        csrf_token:    CSRF,
        full_name:     document.getElementById('fname').value.trim(),
        email:         document.getElementById('femail').value.trim(),
        phone:         document.getElementById('fphone').value.trim(),
        city:          document.getElementById('fcity').value.trim(),
        state:         document.getElementById('fstate').value,
        pan_number:    document.getElementById('fpan').value.trim().toUpperCase(),
        amount:        selAmt,
        donation_type: selDtype,
        campaign:      document.getElementById('fcampaign').value,
        message:       document.getElementById('fmsg').value.trim(),
        wants_80g:     document.getElementById('chk80g').checked ? 1 : 0,
      })
    });

    const order = await res.json();
    if (!res.ok || order.error) throw new Error(order.error || 'Order creation failed.');

    const options = {
      key:         RZP_KEY,
      amount:      order.amount,
      currency:    'INR',
      name:        <?= json_encode(APP_NAME) ?>,
      description: 'Donation — ' + document.getElementById('fcampaign').value,
      order_id:    order.order_id,
      prefill: {
        name:    document.getElementById('fname').value.trim(),
        email:   document.getElementById('femail').value.trim(),
        contact: document.getElementById('fphone').value.trim(),
      },
      theme: { color: '#1D9E75' },
      modal: {
        ondismiss: function() {
          btn.disabled = false;
          btn.textContent = '🔒 Donate Securely';
          showAlert('Payment cancelled. You can try again.', 'warning');
        }
      },
      handler: async function(response) {
        btn.textContent = 'Verifying payment…';
        await verifyPayment(response, order.donation_id);
      }
    };

    const rzp = new Razorpay(options);
    rzp.on('payment.failed', function(resp) {
      showAlert('Payment failed: ' + (resp.error.description || 'Please try again.'));
      btn.disabled = false;
      btn.textContent = '🔒 Donate Securely';
    });
    rzp.open();

  } catch(e) {
    showAlert(e.message || 'Something went wrong. Please try again.');
    btn.disabled = false;
    btn.textContent = '🔒 Donate Securely';
  }
}

async function verifyPayment(rzpResponse, donationId) {
  const res = await fetch(BASE_URL + '/verify_payment.php', {
    method: 'POST',
    // FIX 9 (same): credentials: 'same-origin' so session cookie goes with verify too
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      csrf_token:          CSRF,
      donation_id:         donationId,
      razorpay_order_id:   rzpResponse.razorpay_order_id,
      razorpay_payment_id: rzpResponse.razorpay_payment_id,
      razorpay_signature:  rzpResponse.razorpay_signature,
    })
  });

  const data = await res.json();
  if (!res.ok || data.error) {
    showAlert(data.error || 'Payment verification failed. Contact support.');
    document.getElementById('payBtn').disabled = false;
    document.getElementById('payBtn').textContent = '🔒 Donate Securely';
    return;
  }

  document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
  document.getElementById('stepSuccess').classList.add('active');
  document.querySelectorAll('#progress .step').forEach(s => s.classList.add('done'));

  document.getElementById('suc-heading').textContent = 'Thank you, ' + document.getElementById('fname').value.split(' ')[0] + '!';
  document.getElementById('suc-msg').textContent = 'Your donation of ₹' + selAmt.toLocaleString('en-IN') + ' has been received.';
  document.getElementById('suc-details').innerHTML = `
    <div class="sum-row"><span>Transaction ID</span><strong>${escHtml(rzpResponse.razorpay_payment_id)}</strong></div>
    <div class="sum-row"><span>Order ID</span><strong>${escHtml(rzpResponse.razorpay_order_id)}</strong></div>
    <div class="sum-row"><span>Amount</span><strong>₹${selAmt.toLocaleString('en-IN')}</strong></div>
    <div class="sum-row"><span>Status</span><strong style="color:#1D9E75">✓ Success</strong></div>
  `;
  window.scrollTo(0, 0);
}
</script>
</body>
</html>
