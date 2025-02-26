<script setup>
import Markdown from 'vue3-markdown-it'; /* PartiallyEnd: #3632/scriptSetup.vue */

import AppLayout from '@/Layouts/AppLayout.vue';
const terms = `
# Privacy Notice

**Last Updated: February 1st, 2025**

Pumpfans.io ("we," "us," or "our") is committed to protecting the limited privacy of our users. This Privacy Policy outlines how we collect, use, and protect the limited information we obtain when you use our website, pump.tires (the "Site"), and related services (the "Services"). Please read this Privacy Policy carefully.

## Limited Information Collection

As Pumpfans.io is a non-custodial, transaction templating system, we intentionally minimize the collection of user data. The types of information we may collect are limited to:

### Non-Identifying Usage Data
We may automatically collect certain non-personally identifiable information about your use of the Site, such as:

- Browser type and version
- Operating system
- Device type
- IP address (anonymized or truncated to remove personally identifiable information)
- Referring website
- Pages visited on the Site
- Date and time of access
- Site usage patterns and analytics data (aggregated and anonymized)
- Error logs and technical diagnostics

### Voluntary Communications Data
We may collect limited personal information if you voluntarily provide it to us, such as:

- Your email address if you contact us for support, feedback, or inquiries.
- Any information you choose to include in your communications with us.

We do not collect Know Your Customer (KYC) information, names, addresses, phone numbers, financial details, or any other sensitive personal data unless you explicitly and voluntarily provide it to us for a specific purpose (e.g., support requests). We do not track your blockchain wallet addresses or transaction history on the blockchain.

## Use of Information

The limited information we collect is used solely for the following purposes:

1. **Site Operation and Maintenance**: To operate, maintain, secure, and improve the Site and Services, and to ensure their proper functionality.
2. **Technical Support and Customer Service**: To respond to your inquiries, provide technical support, and address your requests or feedback, if you contact us directly.
3. **Site Analytics and Improvement**: To analyze Site usage trends, monitor performance, diagnose technical issues, and improve the user experience through aggregated and anonymized data analysis.
4. **Security and Abuse Prevention**: To detect, prevent, and investigate potential security breaches, fraud, abuse, or violations of our Terms of Use, and to ensure the security and integrity of the Site and Services.
5. **Legal Compliance**: To comply with applicable laws, regulations, legal processes, or enforceable governmental requests, if required.

## No Sharing of Personal Information (Except Limited Circumstances)

Pumpfans.io is committed to protecting your limited personal information and will not sell, rent, trade, or otherwise disclose your personal information to third parties for marketing or advertising purposes.

We may share your limited information with third-party service providers only to the extent necessary to assist us in operating the Site, providing the Services, and fulfilling the purposes described in Section 2 above. These service providers may include:

- Website hosting providers
- Analytics and site usage tracking services
- Security and fraud prevention service providers
- Email communication and support platforms

These third-party service providers are contractually obligated to protect your information and are prohibited from using your information for their own marketing or other purposes.

We may also disclose your information if required to do so by law, court order, or other legal process, or if we believe in good faith that disclosure is necessary to protect our rights, property, or safety, or the rights, property, or safety of others.

## Data Security

We implement reasonable and appropriate security measures to protect the limited information we collect from unauthorized access, use, disclosure, alteration, or destruction. These measures include technical, administrative, and physical safeguards designed to protect data security and confidentiality. However, no security system is impenetrable, and we cannot guarantee the absolute security of your information.

## Data Retention

We will retain the limited information we collect only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, or as required by applicable law. Aggregated and anonymized data may be retained for longer periods for analytical and statistical purposes.

## "Do Not Track" Signals

Pumpfans.io does not currently respond to "Do Not Track" signals or other mechanisms that provide users with choices regarding the collection of personal information about their online activities over time and across different websites or online services.

## Children's Privacy

Pumpfans.io is not intended for use by children under the age of 18. We do not knowingly collect personal information from children under 18. If we become aware that we have collected personal information from a child under 18, we will take steps to delete such information as soon as possible to the extent permissible by law.

## International Data Transfers

Your information may be processed and stored in servers and locations outside of your country of residence, which may have different data protection laws than your jurisdiction. By using the Site and Services, you consent to the transfer of your information to and processing of your information in such locations.

## Links to Third-Party Websites

The Site may contain links to third-party websites or services that are not owned or controlled by Pumpfans.io. This Privacy Policy does not apply to those third-party websites or services. We encourage you to review the privacy policies of any third-party websites or services you visit.

## Updates to this Privacy Policy

Pumpfans.io reserves the right to modify or update this Privacy Policy at any time, in our sole discretion. If we make material changes to this Privacy Policy, we will provide reasonable notice of such changes, such as by posting the updated Privacy Policy on the Site with a new "Last Updated" date. Your continued use of the Site and Services after any changes become effective constitutes your acceptance of the revised Privacy Policy.

## Contact Us

If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us via our designated support channels, which may be communicated on the Site from time to time.
`;
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray900">
            <div class="max-w-6xl mx-auto px-4 py-8">
                <div class="prose markdown-content dark:prose-invert text-white max-w-none">
                    <Markdown
                        class="gap-3"
                        :source="terms"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>