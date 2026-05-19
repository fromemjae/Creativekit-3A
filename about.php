<?php
// ============================================================
// MASTER CORPORATE PROFILE & INFOCENTER LAYOUT ENGINE (PURPLE TAG INSPIRED)
// about.php
// ============================================================
$pageTitle = "About Us & Corporate Desk | CreativeKit3A";
include 'includes/header.php';
require_once 'includes/db.php';
?>

<main style="background: #f8fafc; font-family: 'Poppins', sans-serif;">
  

  <header style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 60px 20px 50px 20px; text-align: center; border-bottom: 4px solid #FF6B00;">
    <span style="color: #FF6B00; font-weight: 700; font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase; display: block; margin-bottom: 10px;">Discover Who We Are</span>
    <h1 style="margin: 0 0 20px 0; font-size: 2.6rem; font-weight: 900; letter-spacing: -0.5px;">Welcome to <span style="color: #FF6B00;">CreativeKit3A</span></h1>
    <p style="font-size: 1rem; line-height: 1.6; color: #cbd5e1; font-weight: 400; margin: 0 auto; max-width: 750px;">
        <img src="assets/hero.jpg" alt="Custom Corporate Items" style="float: right; width: 390px; height: auto; margin-left: 20px; margin-bottom: 10px; border-radius: 6px;">
      Your ultimate partner in expressing corporate appreciation. We believe in the power of gratitude, and our mission is to help you show your valued clients, stakeholders, partners, and remote employees just how much you appreciate them through thoughtful, tasteful, and meaningful gifts and the latest practical giveaways.
    
    </p>

  </header>

  

  

  <section id="terms-conditions" style="scroll-margin-top: 100px; background: #ffffff; border: 1px solid #e2e8f0; padding: 40px 20px; margin: 20px;">
    <h2 style="margin: 0 0 5px 0; color: #1e293b; font-size: 1.8rem; font-weight: 700;">Terms &amp; <span style="color: #ff6b00;">Conditions</span></h2>
    
    <div style="line-height: 1.7; color: #475569; font-size: 0.95rem;">
        <h4 style="color: #1e293b; font-size: 1.1rem; margin: 20px 0 10px 0;">1. Payment Policy</h4>
      <p style="margin-bottom: 15px;">
        We accept payments through major credit cards, PayPal, bank transfers, money orders, and checks. 
        Orders will only be processed once payment is verified. 
        Delays caused by declined payments or pending transactions are not our responsibility. 
        Applicable taxes may be added based on location.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 20px 0 10px 0;">2. Wholesale Order Placements &amp; Quotations</h4>
      <p style="margin-bottom: 15px;">
        All bulk product customizations require a formal, approved cost breakdown requested through our system's quotation interface. 
        Prices listed under our product catalog reflect base wholesale rates and may fluctuate depending on custom print metrics, 
        coloring options, and volume milestones.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 20px 0 10px 0;">3. Shipping & Handling Policy</h4>
      <p style="margin-bottom: 15px;">
       Orders are processed on business days only, excluding weekends and holidays. Shipping times may vary due to payment verification, customization, courier delays, or customs processing. We are not liable for lost or damaged shipments unless shipping insurance is purchased. Additional shipping fees may apply for incorrect or refused delivery addresses.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">4. Artwork Submissions &amp; Personalization Approvals</h4>
      <p style="margin-bottom: 15px;">
        Clients are fully responsible for the digital design parameters, image files, and corporate logos submitted to our production channels. No printing operations will execute until the digital mock-up layout profile receives a signed confirmation from the client desk.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">5. Copyright Policy</h4>
      <p style="margin-bottom: 15px;">
        Customers are responsible for ensuring that submitted designs, logos, or content do not violate copyright or trademark laws.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">6. Production Quality Policy</h4>
      <p style="margin-bottom: 15px;">
        Product previews and digital mock-ups are for reference only and may slightly differ from the final product. Minor color variations and fading over time are normal. Production defects must be reported within 10 days of delivery for replacement consideration.
      </p>

      <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">7. Ordering Policy </h4>
      <p style="margin-bottom: 15px;">
        Customers may receive a digital proof for approval before production begins. Orders cannot be cancelled once production has started. Cancellation before production may be subject to a processing fee.      
        </p>

        <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">8. Privacy Policy </h4>
      <p style="margin-bottom: 15px;">
        Customer information is kept confidential and will not be sold or shared. We reserve the right to take action against fraudulent or unpaid orders.      
        </p>

        <h4 style="color: #1e293b; font-size: 1.1rem; margin: 25px 0 10px 0;">9. Return Policy </h4>
      <p style="margin-bottom: 15px;">
        Customized products are non-returnable unless there is a production error. Customers are encouraged to review all order details carefully before confirming purchase. Approved issues must be reported within 10 days of receiving the order.      
        </p>

    
    </div>
  </section>

  <section id="sample-works" style="scroll-margin-top: 100px; background: #ffffff; border: 1px solid #e2e8f0; padding: 40px 20px; margin: 20px;">
    <h2 style="margin: 0 0 10px 0; color: #1e293b; font-size: 1.8rem; font-weight: 700;">Our Sample <span style="color: #ff6b00;">Works</span></h2>
    
    <div  class= "sample-card" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
      <figure style="margin: 0; border: 1px solid #e2e8f0; background: #fff; padding-bottom: 12px;">
        <div style="height: 180px; background: #fafafa; text-align: center; padding: 10px; display: flex; align-items: center; justify-content: center;">
          <img src="assets/categories/sub_categories/shirts/dri_fit/dri-fit-shirt.jpg" alt="Dri-Fit Shirt" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <h4 style="margin: 12px 12px 2px 12px; font-size: 0.92rem; color: #1e293b;">Dri-Fit Shirt</h4>
        <span style="margin: 0 12px; font-size: 0.75rem; color: #ff6b00; font-weight: 600; text-transform: uppercase; display: block;">VIEW</span>
      </figure>

      <figure class= "sample-card"  style="margin: 0; border: 1px solid #e2e8f0; background: #fff; padding-bottom: 12px;">
        <div style="height: 180px; background: #fafafa; text-align: center; padding: 10px; display: flex; align-items: center; justify-content: center;">
          <img src="assets/categories/sub_categories/tumblers/stainless_tumblers/drinkware-tumbler.jpg" alt="Stainless Tumbler" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <h4 style="margin: 12px 12px 2px 12px; font-size: 0.92rem; color: #1e293b;">Stainless Tumbler</h4>
        <span style="margin: 0 12px; font-size: 0.75rem; color: #ff6b00; font-weight: 600; text-transform: uppercase; display: block;">VIEW</span>
      </figure>

      <figure class= "sample-card"  style="margin: 0; border: 1px solid #e2e8f0; background: #fff; padding-bottom: 12px;">
        <div style="height: 180px; background: #fafafa; text-align: center; padding: 20px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 3rem;">
          <img src="assets/categories/sub_categories/tote-bags/foldable_totes/tote-bag.jpg" alt="Stainless Tumbler" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <h4 style="margin: 12px 12px 2px 12px; font-size: 0.92rem; color: #1e293b;">Foldable Tote Bag</h4>
        <span style="margin: 0 12px; font-size: 0.75rem; color: #ff6b00; font-weight: 600; text-transform: uppercase; display: block;">VIEW</span>
      </figure>

      <figure class= "sample-card"  style="margin: 0; border: 1px solid #e2e8f0; background: #fff; padding-bottom: 12px;">
        <div style="height: 180px; background: #fafafa; text-align: center; padding: 20px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 3rem;">
            <img src="assets/categories/sub_categories/employee_onboarding/new_hire_sets/employee-onboarding-set.jpg" alt="Stainless Tumbler" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </div>
        <h4 style="margin: 12px 12px 2px 12px; font-size: 0.92rem; color: #1e293b;">New hire welcome kits</h4>
        <span style="margin: 0 12px; font-size: 0.75rem; color: #ff6b00; font-weight: 600; text-transform: uppercase; display: block;">VIEW</span>
      </figure>
    </div>
  </section>

  <section id="contact-desk" style="scroll-margin-top: 100px; background: #ffffff; border: 1px solid #e2e8f0; padding: 40px 20px; margin: 20px;">
    <h2 style="margin: 0 0 10px 0; color: #1e293b; font-size: 1.8rem; font-weight: 700;">Corporate <span style="color: #ff6b00;">Help Desk Registry</span></h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
      <div style="display: flex; flex-direction: column; gap: 15px;">
        <address style="font-style: normal; display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border-left: 4px solid #ff6b00;">
          <i class="fas fa-envelope" style="font-size: 1.3rem; color: #ff6b00;"></i>
          <span>
            <strong style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Mailing Help Desk</strong>
            <a href="mailto:salesandmarketing@creativekit3a.com" style="color: #1e293b; font-weight: 600; text-decoration: none; font-size: 0.95rem;">salesandmarketing@creativekit3a.com</a>
          </span>
        </address>

        <address style="font-style: normal; display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border-left: 4px solid #ff6b00;">
          <i class="fas fa-mobile-alt" style="font-size: 1.3rem; color: #ff6b00;"></i>
          <span>
            <strong style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Smart / Mobile Line</strong>
            <a href="tel:+639177142774" style="color: #1e293b; font-weight: 600; text-decoration: none; font-size: 0.95rem;">+63 917 714 2774</a>
          </span>
        </address>

        <address style="font-style: normal; display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8fafc; border-left: 4px solid #ff6b00;">
          <i class="fas fa-phone" style="font-size: 1.3rem; color: #ff6b00;"></i>
          <span>
            <strong style="display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Globe / Landline Line</strong>
            <a href="tel:+639339927410" style="color: #1e293b; font-weight: 600; text-decoration: none; font-size: 0.95rem;">+63 933 992 7410</a>
          </span>
        </address>
      </div>

      <div style="background: #1e293b; color: #fff; padding: 25px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center;">
        <h4 style="margin: 0 0 10px 0; font-size: 1.1rem; color: #ffea00; font-weight: 600;"><i class="fas fa-clock"></i> Business Operations Schedule</h4>
        <p style="margin: 0 0 8px 0; font-size: 0.9rem; line-height: 1.5; color: #cbd5e1;">Our support staff is active over production networks during standard processing timelines:</p>
        <ul style="margin: 0; padding-left: 20px; font-size: 0.88rem; color: #cbd5e1; line-height: 1.6;">
          <li>Monday to Friday: 8:00 AM – 5:00 PM</li>
          <li>Saturday Sales Desk: 9:00 AM – 1:00 PM</li>
          <li>Sundays &amp; National Holidays: Closed</li>
        </ul>
      </div>
    </div>
  </section>
  

</main>

<?php include 'includes/footer.php'; ?>