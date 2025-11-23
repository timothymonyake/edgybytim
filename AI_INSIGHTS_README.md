# AI Insights Feature - Documentation

## Overview
The AI Insights feature provides intelligent trading analysis powered by OpenAI's GPT-4 model. It offers both on-demand analysis and automated monthly performance reviews.

## Features

### 1. **On-Demand AI Analysis**
- Ask any question about your trading performance
- Get personalized insights based on your recent trade data
- Analyze patterns, weaknesses, and improvement opportunities

### 2. **Monthly Automated Reports**
- Comprehensive AI-powered monthly performance reviews
- Automatically generated on the 1st of each month at 9:00 AM
- Stored in database for historical reference
- Includes:
  - Key strengths and weaknesses
  - Win/loss patterns
  - Actionable recommendations
  - Risk management observations
  - Psychological insights

## 🤖 AI Provider
This feature uses **Groq Cloud API** with the **LLaMA 3.3 70B Versatile** model for high-speed, cost-effective inference.
- **Model**: `llama-3.3-70b-versatile`
- **Endpoint**: `https://api.groq.com/openai/v1/chat/completions`
- **Cost**: Free (currently) or significantly lower than GPT-4.

## 🔑 Setup
1.  **Get an API Key**: Sign up at [Groq Console](https://console.groq.com/keys) and create an API key.
2.  **Configure Profile**: Go to your User Profile -> API Keys tab and paste your Groq API key.
3.  **Enjoy**: Navigate to "AI Insights" to start analyzing your trades.

## Setup Instructions

### 1. Get a Groq API Key
1. Visit [Groq Console](https://console.groq.com/keys)
2. Create an account or sign in
3. Generate a new API key
4. Copy the key (it starts with `gsk_`)

### 2. Configure Your Profile
1. Navigate to **Profile** from the user dropdown menu
2. Scroll to the **API Keys** section
3. Paste your Groq API key
4. Click **Update API Key**

### 3. Start Using AI Insights
1. Click on **AI Insights** from the main menu
2. Choose between:
   - **Ask AI Anything**: Get instant answers to your questions
   - **Monthly Performance Review**: Generate comprehensive monthly reports

## Using the Features

### On-Demand Analysis
1. Type your question in the text area
2. Optionally check "Include my recent trade data" for data-driven insights
3. Click **Generate Insight**
4. Wait for the AI to analyze and respond

**Example Questions:**
- "What are my biggest weaknesses?"
- "How can I improve my win rate?"
- "What patterns do you see in my losing trades?"
- "Am I overtrading?"
- "What's my best trading session?"

### Monthly Reports
1. Click **Generate Monthly Report**
2. The AI will analyze last month's complete trading data
3. Receive a comprehensive report with:
   - Performance summary
   - Detailed analysis
   - Specific recommendations
   - Areas for improvement

## Automated Monthly Insights

### How It Works
The system automatically generates monthly insights for all users with configured API keys on the 1st of each month at 9:00 AM.

### Manual Generation
You can also manually generate monthly insights using the artisan command:

```bash
# Generate for all users
php artisan insights:monthly

# Generate for a specific user
php artisan insights:monthly --user_id=1
```

### Viewing Historical Insights
Monthly insights are stored in the `monthly_insights` table and include:
- Period (e.g., "2025-11")
- AI-generated insight text
- Trade count
- Total P&L
- Win rate

## Scheduling (Cron Setup)

To enable automated monthly insights, add this to your crontab:

```bash
# Edit crontab
crontab -e

# Add this line (adjust path to your project)
* * * * * cd /opt/lampp/htdocs/edgy && php artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's scheduler every minute, which will execute the monthly insights command at the scheduled time.

## API Usage & Costs

### Models Used
- **Model**: `llama-3.3-70b-versatile`
- **Provider**: Groq Cloud

### Estimated Costs
- **Groq API is currently free** (as of late 2025) for limited usage.
- Check [Groq Pricing](https://groq.com/pricing/) for latest details.

**Note**: Costs depend on the amount of trade data and response length. Monitor your usage at [console.groq.com](https://console.groq.com).

## Security

- API keys are stored in the database
- Keys are hidden in the UI (shown as ••••••)
- Keys are only used for AI requests
- Never shared or exposed in responses

## Troubleshooting

### "API Key Required" Error
- Make sure you've added your OpenAI API key in your profile
- Verify the key is correct (starts with `sk-`)

### "No trade data available" Error
- Ensure you have trades recorded for the period being analyzed
- Monthly reports require at least one trade in the previous month

### API Errors
- Check your OpenAI account has sufficient credits
- Verify your API key is still valid
- Check the Laravel logs: `storage/logs/laravel.log`

## Best Practices

1. **Ask Specific Questions**: The more specific your question, the better the AI's response
2. **Include Trade Data**: Enable "Include my recent trade data" for data-driven insights
3. **Review Monthly Reports**: Check your monthly reports regularly for improvement opportunities
4. **Act on Recommendations**: Implement the AI's suggestions and track improvements
5. **Monitor API Usage**: Keep an eye on your OpenAI costs

## Future Enhancements

Potential features for future releases:
- Email delivery of monthly reports
- Custom insight templates
- Multi-month trend analysis
- Comparison with previous months
- Export insights to PDF
- Integration with other AI models

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Laravel logs for detailed error messages
3. Verify your OpenAI API key and account status

---

**Last Updated**: November 22, 2025
**Version**: 1.0
