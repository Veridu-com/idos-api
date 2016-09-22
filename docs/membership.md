Membership
How it works, step by step.

# Administrator
	Request:
		POST /companies/{companySlug}/identities
		Auth: IdentityToken (role - administrator on this company)
		Middleware: EndpointPermission::SELF_ACTION | EndpointPermission::PARENT_ACTION 
			{
				"first_name",
				"last_name",
				"email"
			}
	- idOS: 
		1. Creates a User, then an Identity with "ref":"email:validation_code(generated)", link each other.
			- Has some attributes (first/last name title, etc on the form) that can be added to a source ("input data"?)
		2. Creates a member register. (identity_id, company_id, role) (targetCompany)


# First login:
	0. Dashboard - link: /signup
		[Type your e-mail...] * required
		[Type your validation code...] * required

		[ Facebook ] [ Google ] [ LinkedIn ] [ Amazon ] 

		/sso?email={email}&validation={validation}

	1. Proceed with common SSO Mechanism.
		1.0 Identifies special API Key 
		1.1 Tries to fetch identity with the inpuyts md5("email:validation")
		1.2 create user#1
		1.3 create link#1 { user#1, identity#1 }
		1.4 create source, scrape data.. etc.

	2. SSO will receive a IdentityToken Header param that uses this Identity to add the next created user. (for special keys)
		// same 1.1 response
		// create user#2
		// create link#2 { user#2, identity#1 }
		¹: dashboard will save on browser's storage and will do this is this user already logged on the same browser.
