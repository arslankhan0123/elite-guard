# NFC Integration Documentation: Elite Guard Ecosystem

This document provides technical instructions for integrating physical NFC chips with the **Elite Guard** Admin Panel and your **Flutter** mobile application.

---

## 1. Administrative Workflow (Admin Panel)

Each guard patrol point or site entrance is represented by a unique NFC tag in the database.

### Create and Manage Tags
1. Log into the Admin Panel and navigate to the **NFC Tags** tab.
2. Click **Create** to add a new tag.
3. Select the **Site** where the physical tag will be placed.
4. Use the **"Generate New"** button to create a unique UID (e.g., `NFC-4A2B9C7D`).
5. Click **Save**.
6. In the NFC Listing table, click on the **UID** (in blue) to copy it to your clipboard.

---

## 2. Writing UID to Physical Tags

The UID you copied from the Admin Panel must be written to the physical NFC chip (Card, Sticker, or Token) so the mobile app can read it.

### Required Tools
- **Hardware**: An NFC-enabled smartphone (Android or iPhone).
- **Software**: **"NFC Tools" App** (Recommended) or any NDEF writer available on App/Play Store.
- **Application Link:** https://play.google.com/store/apps/details?id=com.wakdev.wdnfc&hl=en

### Step-by-Step Writing Process
1. Open **NFC Tools** and go to the **Write** tab.
2. Select **Add a record**.
3. Choose **Text** (Simple) or **URL/URI** (Advanced).
   - If using **Text**: The data will be stored as a plain string.
   - If using **URI**: Enter it as `guard://scan?uid=YOUR_UID_HERE`.
4. Click **OK**.
5. Select the **Write** button (it will show a "Ready to scan" prompt).
6. Place your physical NFC tag against the back of your phone.
7. You will see a green checkmark when the UID is successfully written.

---

## 3. Flutter Mobile App Implementation

To read the tag in your Flutter application, use the `nfc_manager` package.

### Configuration

#### **pubspec.yaml**
```yaml
dependencies:
  nfc_manager: ^3.3.0
```

#### **Android Permissions** (`android/app/src/main/AndroidManifest.xml`)
```xml
<uses-permission name="android.permission.NFC" />
<uses-feature name="android.hardware.nfc" android:required="true" />
```

#### **iOS Permissions** (`ios/Runner/Info.plist`)
```xml
<key>NFCReaderUsageDescription</key>
<string>Required to scan patrol points for site security.</string>
```

### Flutter Scanning Code Example

```dart
import 'package:nfc_manager/nfc_manager.dart';

// Start the NFC Scanning Session
void startPatrolScan() {
  NfcManager.instance.startSession(onDiscovered: (NfcTag tag) async {
    try {
      var ndef = Ndef.from(tag);
      if (ndef == null || !ndef.isWritable) {
        print('Tag is not NDEF or Writable');
        return;
      }

      NdefMessage message = await ndef.read();
      for (var record in message.records) {
        // UTF-8 decoding for the payload
        String payload = String.fromCharCodes(record.payload);
        
        // Remove NDEF Text record language length (standard is 3 characters, e.g., 'en')
        String uid = payload.substring(3); 
        
        print('Successfully Scanned: $uid');

        // Call your backend API to register the patrol point scan
        // await submitScanToBackend(uid);
      }

      await NfcManager.instance.stopSession();
    } catch (e) {
      print('NFC Error: $e');
    }
  });
}
```

---

## 4. Suggested API Endpoint Structure

In your Laravel backend, you should expose an endpoint for the Flutter app to submit the scanned UID.

**Endpoint**: `POST /api/v1/patrol/scan`  
**Request Body**:
```json
{
    "nfc_uid": "NFC-A1B2C3D4",
    "guard_id": 45,
    "scanned_at": "2026-04-02 23:10:00"
}
```

**Controller Logic Snippet**:
```php
public function registerScan(Request $request) {
    $tag = NfcTag::where('uid', $request->nfc_uid)->first();
    if (!$tag) {
        return response()->json(['error' => 'Invalid Tag'], 404);
    }
    // Logic to record the patrol visit...
}
```

---

## ⚠️ Summary of Best Practices
1.  **Tag Locking**: Once you write the final UID to a chip, use **NFC Tools** to "Lock" the tag to prevent unauthorized modification.
2.  **Metadata**: Include the Site name on the physical card or sticker so guards know which point they are scanning.
3.  **Authentication**: Ensure the Flutter app is logged in before allowing a scan to prevent spoofing.














## 5. Mobile App Workflow (Roman Urdu)

### **Mobile App Workflow (UID Text Scan):**

1.  **Guard App Ko Khulega**: Guard ko sab se pehle apni app open karni hogi.
2.  **Scan Button Tapping**: App ke andar ek button hoga (e.g., **"Scan Patrol Point"**). Guard uss par click karega.
3.  **Ready to Scan Alert**: App ek popup ya message dikhaye gi: *"Hold your phone near the NFC Tag"*.
4.  **Physical Scan**: Guard apna phone physical tag ke paas le jaye ga.
5.  **Data Extraction**: Flutter app tag ke andar se woh text reading karegi (e.g., `NFC-ABC12345`).
6.  **Backend Verification**: App woh UID aapke Laravel API ko bheje gi (e.g., `POST /api/v1/scan`).
7.  **Attendance/Patrol Record**: Laravel backend database mein check karega ke ye UID kis "Site" ka hai, aur guard ki attendance ya patrol record kar lega.
8.  **Success Message**: App Guard ko show karegi: *"Patrol Point Verified! ✅"*.

### **Important Technical Detail (Payload Prefix):**
Jab aap NFC Tools mein **"Text"** record likhte hain, to NFC standard ke mutabiq woh payload ke shuru mein **language code** (jaise `en`) add kar deta hai. 

Iska matlab hai jab aapki Flutter app UID read karegi, to use shuru ke kuch characters remove karne parenge taakay asali UID mil sake. 

**Code example (Flutter):**
```dart
// Agar payload "enNFC-12345" hai
// to asali UID nikalne ke liye:
String uid = payload.substring(3); // "NFC-12345"
```

> [!TIP]  
> **Asaani ke liye mashwara:** Agar aap chahte hain ke bilkul waisa hi text mile jo aapne likha hai, to NFC Tools mein **"Custom URL / URI"** select karein aur wahan sirf apna UID likh den (bina scheme ke). Is se app ko pure UID mil jayega.
