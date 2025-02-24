=== Zara Chat ===
Contributors: omayib
Tags: chat, rag, navigation
Requires at least: 5.7
Tested up to: 6.7
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Zara is an AI-powered WordPress plugin that makes content discovery interactive and fun.

== Description ==

Have you ever landed on a website, typed something into the search bar, and still couldn’t find what you were looking for? Zara is here to solve that problem. It’s a WordPress plugin that brings AI and powerful vector-based search to your site.

Better Search: Think of it as giving your site a friendly little helper that can instantly understand what users are looking for.
Quick Content Generation: Zara uses AI to help you create fresh, engaging text—great for blog posts, product descriptions, or general site content.
Smarter Engagement: By offering conversational answers, Zara keeps people on your site longer (hello, lower bounce rates!).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `Zara_Vx.x.x` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `[chat_widget]` in your templates

== Frequently Asked Questions ==
=1. What exactly is Zara Chat, and how does it differ from regular live chat plugins?=
Answer: Zara Chat uses AI-driven, Retrieval Augmented Generation (RAG) technology. It doesn’t just let users message you—it also analyzes your site’s content (stored in a vector database) and provides relevant answers in real time. This advanced approach goes beyond typical live chat by offering immediate, context-rich responses without human intervention.

=2. Do I need any external services to run Zara Chat?=
Answer: Yes. Zara Chat connects with AI services like OpenAI for text generation and vector databases (e.g., Pinecone) for semantic search. You’ll need API keys for these services, which you can obtain from their respective platforms.

=3. Is coding experience required to set up Zara Chat?=
Answer: No coding is necessary. Once you’ve installed the plugin and entered your API credentials (OpenAI key, Pinecone Host, etc.), Zara Chat handles the rest. All settings are accessible through the WordPress admin dashboard.

=4. How does Zara Chat handle privacy and data security?=
Answer: Zara Chat primarily processes content you choose to index or generate. It’s important to store sensitive data securely and only enable indexing for non-confidential pages. The plugin itself doesn’t retain personal data about your users; however, you should review and follow GDPR or other relevant data privacy regulations for any third-party services you connect.

=5. Can I customize Zara Chat’s tone and responses?=
Answer: Absolutely. Within the plugin settings, you can tweak various parameters—like “temperament” or “creativity”—to match your brand’s voice. You can also select which pages or posts are included in the AI’s knowledge base, ensuring only the most relevant and on-brand information is used to generate responses.

== Screenshots ==
1. Zara chat embbeded on landing page section
2. Zara chat embedded as floating chat 
3. Zara chat selection indexing


== Changelog ==
= 1.0.1 =
* Initial release.