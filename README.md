## Simple button demo

### Scenarios 1: Login First, Account Registration, Linking and Purchase
1. Consumers chooses to buy and enters the checkout process
1. A page showing the following options: Sign-in (native), Log In with PayPal, Register (native) or Guest checkout
1. Consumer clicks on the Log in with PayPal button:
	1. PayPal Access Login page (checks “Remember me”)
	1. If first time, Consent page
1. Consumer sees one of the 3 pages on demo site
	1. where Merchant offers to link to existing account (if email match is found)
		1. Consumer has an option to link or skip
			1. Link: Consumer logs in with merchant password, link confirmation or try again
			1. Skip: Consumer is notified that prior history will be only accessible through the original login.
		1. Where Merchant offers to create a new account, with the escape option to link to existing (acknowledgement of account creation may not need to be an additional page)
			1. Consumer sees all fields prefilled with the option to edit
			1. Consumer clicks action (Create, or Register)
		1. If consumer is returning (account already linked), consumer goes to where they came from
1. Consumer clicks on Pay with PayPal button
	1. Consumer sees RYP page, agrees to pay and returns back to the demo site
	1. Consumer confirms the order
	1. Consumer sees the success page

### Scenario 2: Guest Checkout – assuming no login is required to purchase:
1. Consumer comes to a site, adds to cart, and clicks on Pay with PayPal button
1. Consumer logs in with EC and sees RYP
1. Consumer is back on a page where he sees one of the following:
	1. Confirm your order page
	1. Success page where user sees
		1. An option to create account using Log in with PayPal
		1. Based on the email match, an option to Log in with PayPal to add this purchase to the order history (?)
