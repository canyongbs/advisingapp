<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Consent\Database\Seeders;

use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Consent\Models\ConsentAgreement;
use Illuminate\Database\Seeder;

class ConsentAgreementSeeder extends Seeder
{
    public function run(): void
    {
        // Artificial Intelligence Usage Agreement
        ConsentAgreement::factory()
            ->create([
                'title' => 'Acceptable Use Policy for AI Assistant',
                'description' => 'Please review and agree to the acceptable use policy (AUP) in order to use this technology.',
                'body' => <<<EOT
                PLEASE READ THIS POLICY CAREFULLY. BY CLICKING "I ACCEPT" OR OTHERWISE USING ANY PART OF THIS ENTERPRISE ARTIFICIAL INTELLIGENCE (“AI”) ASSISTANT TOOL, YOU ARE AGREEING TO THE TERMS OF THIS POLICY. If you do not agree to these terms, please do not use this AI Assistant tool.

                Welcome to [Institution Name]'s AI Assistant Tool. This Acceptable Use Policy ("Policy”) governs the use of generative AI assistant tools (“AI Assistant Tool”), including but not limited to the Advising App by Canyon GBS™, by all employees (“You” or “Your”). By using the AI Assistant Tool, You agree to comply with this Policy. Failure to comply may result in disciplinary action, up to and including termination of access and legal consequences.

                Acceptable Uses

                1.1. Authorized Users. The AI Assistant Tool is provided for use by [Institution Name] for legitimate institution-related purposes only. The AI Assistant Tool is to be utilized exclusively for professional activities that directly support Your duties at [Institution Name]. The use of these technologies for personal gain or activities outside the scope of Your responsibilities during work hours is strictly prohibited.

                1.2. Professional Conduct. You are expected to use the AI Assistant Tool in a professional manner and in accordance with [Institution Name]’s policies and procedures. [Optional: Insert link to applicable institution policies and procedures here]

                1.3. Data Privacy. You shall respect the privacy of individuals and confidential information. Do not use the AI Assistant Tool to access, share, or distribute sensitive or confidential information without proper authorization.

                1.4. Compliance with Law. You shall comply with all applicable laws, regulations, and [Institution Name] policies when using the AI Assistant Tool.

                Prohibited Uses
                2.1. Automated Processes with Legal Consequences: Do not use the AI Assistant Tool in automated decision-making processes with legal or similarly significant ramifications. In these situations, the final decision must involve a human being. You must consider factors beyond the recommendations provided by the AI Assistant Tool.

                2.2. Deceptive Activity: Do not engage in generating deceptive or fraudulent content, including:

                2.2.1. Creating deepfakes intended to deceive or manipulate.

                2.2.2. Producing content that impersonates individuals or misrepresents their identity.

                2.2.3. Misrepresenting content created through automated processes as if it were human-generated or original content.

                2.2.4. Participating in plagiarism or academic dishonesty.

                2.3. High-Risk Activities: Do not use the AI Assistant Tool for high-risk activities. The AI Assistant Tool is not completely fault-tolerant and is not designed or intended for use in any high-risk applications that could lead to death, serious bodily injury, or catastrophic damage.

                2.4. Illegal Activities: Do not engage in generating:

                2.4.1. Malicious code, malware, or other harmful software.

                2.4.2. Content that promotes hate speech, violence, discrimination, or child sexual exploitation and abuse.

                2.4.3. Violations of privacy or intellectual property rights.

                2.4.4. Violations of confidentiality agreements.

                2.4.5. Violations of software or system access and permissions.

                2.5. Licensed Professional: Do not generate individualized advice of a nature that, in the ordinary course of business, would typically be dispensed by a licensed professional. This includes advice related to financial, legal, or medical matters.

                2.6. Misuse of Information: Do not use the AI Assistant Tool to collect, store, or disseminate personal information without proper authorization or in violation of privacy laws.

                2.7. Political Campaigns: Do not use the AI Assistant Tool for political campaigns, including:

                2.7.1. Creating, disseminating, or targeting political advertisements, content, or messaging.

                2.7.2. Soliciting funding for political campaigns.

                2.7.3. Influencing, manipulating, or swaying political opinions, voting behavior, or electoral outcomes

                2.8. Predicting Protected Characteristics: Do not explicitly predict any individual's protected characteristics, such as racial or ethnic origin, political opinions, religious or philosophical beliefs, age, gender, sexual orientation, disability, health status, medical condition, financial status, trade union membership, criminal convictions, or propensity to engage in criminal behavior.

                2.9. Sexual Content: Do not engage in the creation, dissemination, or facilitation of adult content, adult industry products or services, or dating applications related to adult entertainment.

                2.10. Unauthorized Users: The AI Assistant Tool provided by [Institution Name] is only for use by authorized staff members. Sharing or allowing others to use it without written consent from [AI Tool Administrator Name/Department] is strictly prohibited. Unauthorized use may result in disciplinary action.

                Monitoring and Enforcement. [Institution Name] may monitor the use of the AI Assistant Tool to ensure compliance with this Policy and applicable laws. Violations of this Policy may result in disciplinary action, including but not limited to temporary or permanent suspension of access to the AI Assistant Tool, and legal action as deemed appropriate.

                Disclaimer and Notices.

                4.1. Disclaimer. The AI Assistant Tool relies on technology that is still developing, and it may produce unexpected or inaccurate output. By using the AI Assistant Tool, You acknowledge that You are aware of and accept these risks.

                4.2. Inherit Bias Notice. AI systems may inherit biases from the data and Large Language Model (LLM) they are trained on. You are reminded to be vigilant about potential biases in AI-generated content or decision-making processes to avoid unintended discrimination or misrepresentation.

                4.3. Quality Control Notice. Although AI can automate numerous tasks, human oversight remains essential. You bear the responsibility of reviewing and editing AI-generated content to ensure it aligns with the [Institution Name]’s voice, values, and quality standards, maintaining the integrity and reputation of the institution.

                4.4. Over-Reliance on AI Notice. While AI tools offer support, they should not substitute for Your individual creativity, strategic thinking, and intuition, recognizing the importance and value of Your input in decision-making processes is an important part of using the AI Assistant Tool effectively. You are encouraged to strike a balance and utilize AI as a tool to enhance, not replace, Your expertise.

                Modification of Policy: [Institution Name] reserves the right to modify or update this Policy at any time. You will be notified of any changes, and continued use of the AI Assistant Tool after such modifications constitute acceptance of the revised Policy.

                By clicking "I ACCEPT" and using the AI Assistant Tool, You acknowledge that You have read, understood, and agree to abide by this Policy.
                EOT,
                'type' => ConsentAgreementType::AzureOpenAI,
            ]);
    }
}
