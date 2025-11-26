# 🎓 Collexia Integration Explained Like You're in High School

## 📖 The Big Picture: What Problem Are We Solving?

Imagine you're a landlord who rents out rooms to students. Every month, you need to collect rent from 100 students. That's a LOT of work:
- You have to remind each student
- You have to wait for them to pay
- You have to track who paid and who didn't
- You have to chase late payments

**Collexia is like a robot assistant that automatically collects rent from students' bank accounts every month!**

---

## 🏦 What is Collexia?

**Collexia** is a payment collection service (like a digital debt collector, but friendly). It's a company that specializes in automatically taking money from people's bank accounts when they owe money.

Think of it like:
- **Netflix** automatically charges your card every month
- **Spotify** automatically charges your card every month
- **Collexia** automatically charges students' bank accounts every month for rent

---

## 📝 What is a MANDATE? (The Most Important Concept!)

### Simple Definition:
A **mandate** is like **permission slip** that says:
> "Hey bank, you have my permission to automatically take money from my account every month and give it to my landlord."

### Real-World Analogy:
Remember when you were a kid and your mom signed a permission slip for a school trip? That slip gave the teacher permission to take you on the trip.

A **mandate** is like that, but for money:
- The **student** signs the mandate (gives permission)
- The **bank** keeps the mandate on file
- Every month, the bank automatically takes the rent money
- The **landlord** (you) gets paid automatically

### What Information is in a Mandate?

Think of a mandate like a contract with these details:

1. **WHO** is paying? (Student's name, ID number, bank account)
2. **HOW MUCH** will be taken? (e.g., N$2,500 per month)
3. **WHEN** will it be taken? (e.g., 1st of every month)
4. **HOW OFTEN**? (Monthly, weekly, etc.)
5. **FOR HOW LONG**? (e.g., 12 months = 12 payments)
6. **WHAT FOR?** (Rent for Property XYZ)

---

## 🔄 How Does the Whole System Work?

### Step-by-Step Flow:

#### 1️⃣ **Student Registers** (Your App)
```
Student opens your app → Fills in their details → Submits
```
- Name, email, phone
- ID number
- Bank account details
- Which property they're renting

#### 2️⃣ **Create a Rental Agreement** (Your Database)
```
Your system creates a "rental" record
```
- Links student to property
- Sets monthly rent amount
- Sets start date
- Creates a unique contract reference number

#### 3️⃣ **Register the Mandate** (Send to Collexia)
```
Your system → Collexia API → "Hey, register this mandate!"
```
This is like sending the permission slip to the bank. You're telling Collexia:
- "This student wants to pay rent automatically"
- "Here are all their details"
- "Start collecting on [date]"

#### 4️⃣ **Collexia Processes the Mandate**
```
Collexia → Student's Bank → "Is this mandate valid?"
```
- Collexia checks if the student's bank account exists
- Verifies the student's ID
- Confirms the mandate is valid
- Stores it in their system

#### 5️⃣ **Mandate Status Check**
```
Your system → Collexia API → "What's the status of mandate XYZ?"
```
Collexia responds with:
- ✅ **Active** = Everything is good, money will be collected
- ❌ **Rejected** = Something went wrong (wrong account, insufficient funds, etc.)
- ⏳ **Pending** = Still being processed

#### 6️⃣ **Automatic Collection** (Every Month)
```
1st of the month → Collexia → Bank → "Take N$2,500 from Student A's account"
```
- Collexia automatically tells the bank to take the money
- Bank takes the money from student's account
- Money goes to your account (the landlord)

#### 7️⃣ **Payment Notification** (You Get Paid!)
```
Collexia → Your system → "Payment received! Student A paid N$2,500"
```
- You can check payment history
- See who paid and who didn't
- Track everything in your system

---

## 🏗️ What Are the Main Components?

### 1. **Your PHP API** (The Middleman)
Think of this as a **translator** between your app and Collexia:
- Your app speaks "student language" (student_id, property_id, etc.)
- Collexia speaks "bank language" (clientNo, contractReference, etc.)
- Your API translates between them

**Key Files:**
- `api/controllers/MandateController.php` - Handles mandate operations
- `src/CollexiaClient.php` - Talks to Collexia's servers
- `config.php` - Stores your Collexia credentials

### 2. **Your Database** (The Memory)
Stores all your information:
- **Students** table - All student details
- **Properties** table - All rental properties
- **Rentals** table - Which student rents which property
- **Mandates** table - All the mandates you've registered
- **Payments** table - Payment history

### 3. **Collexia API** (The Payment Processor)
Collexia's servers that:
- Accept mandate registrations
- Process payments
- Send payment notifications
- Provide payment history

---

## 🔐 Security: How Do We Talk to Collexia Safely?

Collexia is VERY strict about security. You can't just call them like calling a friend. You need:

### 1. **Basic Authentication** (Username & Password)
```
Username: bareinvuat
Password: Ms@utbinT!11
```
Like logging into your email - you need the right username and password.

### 2. **Digital Signature** (Like a Secret Handshake)
Every request needs a special "signature" that proves it's really you:
- **Client ID**: `6FA41D83-B8A5-11F0-B138-42010A960205` (like your ID card number)
- **Client Secret**: `9FXhhuOtjiKinPFpbnSb` (like a secret password)
- **HMAC-SHA512**: A fancy way of creating a unique signature for each request

Think of it like:
- You write a letter
- You sign it with a special pen that only you have
- Collexia checks the signature to make sure it's really from you

### 3. **Special Headers** (Required Information)
Every request must include:
- `CX_SWITCH_ClientId` - Your client ID
- `CX_SWITCH_DTS` - Current timestamp (when you sent the request)
- `CX_SWITCH_HSH` - The digital signature (HMAC)

---

## 📊 Key Terms Explained Simply

### **Contract Reference**
A unique ID for each rental agreement. Like a receipt number.
- Example: `1258420241114001`
- Format: `MerchantGID + Date + Sequence Number`

### **User Reference**
A shorter version of the contract reference (last 6 digits).
- Example: `240001`
- Used for quick lookups

### **Client Number (clientNo)**
The student's ID in your system.
- Example: `STU001`
- Must be unique and alphanumeric (max 15 characters)

### **Frequency Code**
How often money is collected:
- `1` = Daily
- `2` = Weekly
- `3` = Bi-weekly (every 2 weeks)
- `4` = Monthly (most common for rent)
- `5` = Quarterly (every 3 months)
- `6` = Annually

### **MAG ID**
The type of mandate:
- `46` = Endo (Electronic Debit Order) - Most common
- Other codes for different payment types

### **Merchant GID**
Your company's ID number in Collexia's system.
- Yours: `12584`
- Like a business registration number

### **Remote GID**
Your location/branch ID.
- Yours: `71`
- If you had multiple offices, each would have a different Remote GID

---

## 🔄 The Complete Flow in Simple Terms

### Scenario: Student "John" wants to rent "Room 101" for N$2,500/month

1. **John signs up in your app**
   - Enters his details
   - Selects Room 101
   - Agrees to pay N$2,500/month

2. **Your system creates a rental**
   - Links John to Room 101
   - Creates contract reference: `1258420241114001`
   - Sets start date: 2024-12-01

3. **Your system registers a mandate with Collexia**
   ```
   "Hey Collexia, John wants to pay automatically:
   - Take N$2,500 from his account
   - Every month on the 1st
   - For 12 months
   - Starting December 1st, 2024"
   ```

4. **Collexia checks with John's bank**
   - "Does this account exist?" ✅
   - "Is John's ID valid?" ✅
   - "Can we set up automatic payments?" ✅

5. **Collexia confirms: Mandate is ACTIVE**
   - Your system saves this status
   - John gets a notification: "Your automatic payment is set up!"

6. **Every month (automatically):**
   - December 1st: Collexia takes N$2,500 → You get paid ✅
   - January 1st: Collexia takes N$2,500 → You get paid ✅
   - February 1st: Collexia takes N$2,500 → You get paid ✅
   - ... and so on for 12 months

7. **If payment fails:**
   - Collexia tries again (usually 3 times)
   - You get notified: "John's payment failed"
   - You can contact John to fix the issue

---

## 🎯 What Can You Do With This System?

### ✅ **Register Mandates**
Set up automatic payments for students

### ✅ **Check Mandate Status**
See if a mandate is active, rejected, or pending

### ✅ **Cancel Mandates**
Stop automatic payments (if student moves out)

### ✅ **View Payment History**
See all payments that were collected

### ✅ **Track Payments**
Know exactly who paid and when

---

## 🚨 Common Issues & What They Mean

### "Mandate amount limit exceeded"
- **Meaning**: The amount you're trying to collect is too high
- **Solution**: Reduce the amount (for UAT testing, use N$100)

### "Unexpected format for value of field, clientNo"
- **Meaning**: The student ID format is wrong
- **Solution**: Use alphanumeric, max 15 characters (e.g., `STU001`)

### "401 Unauthorized"
- **Meaning**: Wrong username, password, or digital signature
- **Solution**: Check your credentials in `config.php`

### "404 Not Found"
- **Meaning**: Wrong URL or endpoint
- **Solution**: Check the API endpoint path

---

## 💡 Key Takeaways

1. **Mandate = Permission** for automatic bank payments
2. **Collexia = The Service** that handles automatic collections
3. **Your API = The Translator** between your app and Collexia
4. **Security = Very Important** (username, password, digital signature)
5. **Automatic = Set it once, it works every month**

---

## 🎓 Think of It Like This:

**Without Collexia:**
- You: "Hey John, pay your rent!"
- John: "Okay, I'll do it later..."
- You: "Did you pay yet?"
- John: "Oh, I forgot..."
- 😫 Stressful and time-consuming

**With Collexia:**
- You: Set up mandate once
- Collexia: Automatically collects every month
- You: Get paid automatically
- 😊 Easy and stress-free!

---

## 📚 Summary in One Sentence:

**Collexia integration lets you automatically collect rent from students' bank accounts every month, without having to remind them or chase payments!**

---

*Remember: A mandate is just a fancy word for "permission to automatically take money from a bank account." That's it!*

