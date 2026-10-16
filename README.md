# Zara – The Future of Content Navigation

Hey there, content connoisseurs and tech aficionados! Welcome to **Zara** – the groundbreaking WordPress plugin that transforms the old, mundane menu navigation into an interactive quest for the perfect content. Forget aimlessly clicking around; with Zara, you simply answer a few fun questions, and voilà – your content is served on a silver platter!

<div align="center">
  <img src="documentation/images/bannerhizara.png" alt="screenshot" width="400"/>
</div>

---

## Description

Zara is not your average plugin – it's a cutting-edge, AI-powered assistant that makes content discovery a breeze. Leveraging the magic of Langchain and LLM support, Zara turns your website into an intelligent guide that understands and directs your users to exactly what they need. And that's not all:

- **Seamless Integration:** Connects with Pinecone for vector storage.
- **Smart AI:** Utilizes OpenAI for embeddings and completions.
- **Interactive Experience:** Engages users with fun, dynamic questions.

Think of Zara as your personal digital concierge, making content exploration fun, fast, and fabulously smart!

---

## Installation

Ready to supercharge your WordPress site with the power of AI? Follow these simple steps to install Zara:

1. **Download & Upload:**
   - Download the latest version of Zara.
   - Log in to your WordPress dashboard, navigate to **Plugins > Add New > Upload Plugin**, and select the Zara zip file or
   - Visit [omayib.github.io/zara](https://omayib.github.io/zara) and click **Download plugin**.

2. **Activate the Plugin:**
   - Once uploaded, click **Activate** to unleash Zara’s capabilities on your site.

3. **Configure Your Settings:**
   - Go to the Zara settings panel in your dashboard.
   - Connect your Pinecone and OpenAI accounts by entering your API keys.
   - Customize the interactive questions to match your content style and let Zara work its magic.

4. **Enjoy the Magic:**
   - Sit back and watch as Zara transforms your site into an engaging, intelligent content haven!

---

Get ready to reimagine content navigation – with Zara, your website is about to get a whole lot smarter and a whole lot cooler!

---
# For Developer
You can manage this projects directory separately instead `sites_name/app/public/wp-content/plugin` directory by using symbolic link. Here is the way for create symbolic link.
```
ln -s ~/Workspace/Zara/ Zara
```

Where `~/Workspace/Zara/` is directory outside wp. Meanwhile, `Zara` is directory inside `plugin`

Use *Local* from https://localwp.com/ for managing localhost wordpress project. Monitor the error log by using
```
tail -f ~/Local\ Sites/lookingforsite/logs/php/error.log
```