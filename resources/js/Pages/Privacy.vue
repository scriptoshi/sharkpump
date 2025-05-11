<script setup>
import Markdown from "vue3-markdown-it"; /* PartiallyEnd: #3632/scriptSetup.vue */

import AppLayout from "@/Layouts/AppLayout.vue";
const terms = `
# Privacy Policy

**Last Updated: February 1st, 2025**

At Sharkpump.io ("we," "us," "our"), a non-custodial platform, we are deeply committed to safeguarding the limited privacy of our users. This Privacy Policy details how we collect, use, and protect the minimal information obtained when you interact with our website, pump.tires (the "Site"), and associated services (the "Services"). Please review this policy carefully.

## Limited Information Collection

As a non-custodial transaction templating system, Sharkpump.io intentionally minimizes user data collection. The information we may gather is strictly limited to:

### Non-Identifying Usage Data

We automatically collect limited non-personally identifiable usage data, including:

* Browser and operating system details
* Device type
* Anonymized or truncated IP address
* Referring website
* Pages viewed, date, and time of access
* Aggregated and anonymized site usage patterns
* Technical error logs

### Voluntary Communications Data

Should you choose to contact us for support, feedback, or inquiries, we may collect voluntarily provided communication data, such as your email address and any information you include in your message.

**We do not collect Know Your Customer (KYC) information, names, addresses, phone numbers, financial details, or other sensitive personal data unless explicitly and voluntarily provided for a specific purpose (e.g., support requests). We do not track your blockchain wallet addresses or transaction history on the blockchain.**

## Use of Information

The minimal information we collect is used exclusively for these purposes:

1.  **Operating & Improving the Site:** To ensure the proper functionality, security, and continuous improvement of our Site and Services.
2.  **Providing Support:** To respond to your direct inquiries, offer technical assistance, and address your feedback.
3.  **Analyzing Site Performance:** To monitor usage trends, diagnose issues, and enhance the user experience through aggregated, anonymized data.
4.  **Ensuring Security:** To detect, prevent, and investigate security breaches, fraud, abuse, or violations of our Terms of Use, thereby maintaining the integrity of our Services.
5.  **Meeting Legal Obligations:** To comply with applicable laws, regulations, legal processes, or government requests when necessary.

## No Sharing of Personal Information (Except Limited Circumstances)

Sharkpump.io is committed to safeguarding your limited personal information and will **never sell, rent, trade, or disclose it to third parties for marketing or advertising purposes.**

We may share your limited information with trusted third-party service providers only when essential for operating the Site, delivering our Services, and achieving the purposes outlined in Section 2. These providers include (but are not limited to):

* Website hosting and infrastructure providers
* Analytics and site usage tracking services
* Security and fraud prevention services
* Email communication and support platforms

These service providers are strictly obligated by contract to protect your information and are prohibited from using it for their own marketing or other purposes.

Information may also be disclosed if legally required by law, court order, or similar process, or if we genuinely believe such disclosure is necessary to protect our rights, property, or safety, or the rights, property, or safety of others.

## Data Security

We employ reasonable and appropriate security measures—including technical, administrative, and physical safeguards—to protect the limited information we collect from unauthorized access, use, disclosure, alteration, or destruction. While we strive for robust protection, no security system is entirely impenetrable, and absolute data security cannot be guaranteed.

## Data Retention

We retain the limited information we collect only for as long as necessary to fulfill the purposes described in this Privacy Policy or as required by applicable law. Aggregated and anonymized data may be kept for longer periods for analytical and statistical insights.

## "Do Not Track" Signals

Sharkpump.io currently does not respond to "Do Not Track" signals or similar mechanisms that offer users choices regarding the collection of personal information across different online services over time.

## Children's Privacy

Sharkpump.io is not intended for users under 18 years of age. We do not knowingly collect personal information from children under 18. If we discover that such information has been collected, we will promptly take steps to delete it, to the extent permissible by law.

## International Data Transfers

Your information may be processed and stored on servers located outside your country of residence, which may have different data protection laws. By using our Site and Services, you consent to the transfer and processing of your information in these locations.

## Links to Third-Party Websites

Our Site may include links to third-party websites or services not owned or controlled by Sharkpump.io. This Privacy Policy does not extend to those external sites or services. We advise you to review the privacy policies of any third-party sites you visit.

## Updates to this Privacy Policy

Sharkpump.io reserves the right to modify or update this Privacy Policy at our sole discretion at any time. For material changes, we will provide reasonable notice, such as by posting the updated policy on the Site with a revised "Last Updated" date. Your continued use of the Site and Services after such changes signifies your acceptance of the revised Privacy Policy.

## Contact Us

For any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us through our designated support channels, as communicated on the Site..
`;
</script>

<template>
	<AppLayout>
		<div class="min-h-screen bg-gray900">
			<div class="max-w-6xl mx-auto px-4 py-8">
				<div
					class="prose markdown-content dark:prose-invert text-white max-w-none"
				>
					<Markdown class="gap-3" :source="terms" />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
