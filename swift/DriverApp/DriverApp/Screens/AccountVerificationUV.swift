//
//  AccountVerificationUV.swift
//  DriverApp
//
//  Created by NEW MAC on 09/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AccountVerificationUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    
    
//    var userProfileJsonDict:NSDictionary?
    
    @IBOutlet weak var scrollView: UIScrollView!
    
    var mainScreenUv:MainScreenUV?
    var menuScreenUv:MenuScreenUV?

    @IBOutlet weak var smsView: UIView!
    @IBOutlet weak var smsViewHeight: NSLayoutConstraint!
    @IBOutlet weak var samsHeaderLbl: MyLabel!
    @IBOutlet weak var smsTxtField: MyTextField!
    @IBOutlet weak var smsOkBtn: MyButton!
    @IBOutlet weak var smsMobileNumLbl: MyLabel!
    @IBOutlet weak var smsSentLbl: MyLabel!
    @IBOutlet weak var smsResendBtn: MyButton!
    @IBOutlet weak var smsMobileEditBtn: MyButton!
    @IBOutlet weak var smsHelpLbl: MyLabel!
    
    @IBOutlet weak var emailView: UIView!
    @IBOutlet weak var emailViewHeight: NSLayoutConstraint!
    @IBOutlet weak var emailHeaderLbl: MyLabel!
    @IBOutlet weak var emailTxtField: MyTextField!
    @IBOutlet weak var emailOkBtn: MyButton!
    @IBOutlet weak var emailSentLbl: MyLabel!
    @IBOutlet weak var emailIdLbl: MyLabel!
    @IBOutlet weak var emailResendBtn: MyButton!
    @IBOutlet weak var emailEditBtn: MyButton!
    @IBOutlet weak var emailHelpLbl: MyLabel!
    @IBOutlet weak var demoHintLbl: MyLabel!
    @IBOutlet weak var demoHintLblHeight: NSLayoutConstraint!
    
    var isSignUpPage = false
    var isFbVerifyPage = false
    var isEditProfile = false
    var isAccountInfo = false
    var isMainPage = false
    
    var emailVerificationCode = ""
    var smsVerificationCode = ""
    
    var mobileNumVerified = false
    var emailIdVerified = false
    
    var requestType = ""
    
    let generalFunc = GeneralFunctions()
    var userProfileJson:NSDictionary!
    
    var mobileNum = ""
    
    var isFirstLaunch = true
    var isFirstVerification = true
    
    var isEditInfoTapped = false
    
    var VERIFICATION_CODE_RESEND_TIME_IN_SECONDS = 0
    var VERIFICATION_CODE_RESEND_RUNNING = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    func setScreenHeight(){
        self.smsViewHeight.constant = smsView.isHidden == false ? (235 + self.smsHelpLbl.frame.size.height - 20) : 0
        
        self.emailViewHeight.constant = emailView.isHidden == false ? (235 + self.emailHelpLbl.frame.size.height - 20) : 0
        
        self.scrollView.contentSize = CGSize(width: self.scrollView.frame.width, height: self.smsViewHeight.constant + self.emailViewHeight.constant + 40)
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.addBackBarBtn()
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AccountVerificationScreenDesign", uv: self, contentView: contentView))

        setData()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    override func viewDidAppear(_ animated: Bool) {
        
        if(isFirstLaunch){
            
            
            setScreenHeight()
            
//            self.scrollView.setContentViewSize(offset: 25 + self.smsHelpLbl.frame.size.height + self.emailHelpLbl.frame.size.height)
            
            isFirstLaunch = false
        }
        
        
    }

    override func closeCurrentScreen()
    {
        
        if VERIFICATION_CODE_RESEND_RUNNING == true
        {
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Are you sure you want to cancel current running request's process?", key: "LBL_CANCEL_VERIFY_SCREEN_PROCESS_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_CONTINUE_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedId) in
                
                if(btnClickedId == 0){
                    
                    self.closeCurrentScreen()
                }
            })
        }
        else
        {
            super.closeCurrentScreen()
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_TXT")
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        samsHeaderLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_MOBILE_VERIFy_TXT")
        smsSentLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_SMS_SENT_TO")
        samsHeaderLbl.textColor = UIColor.UCAColor.AppThemeColor_1
        emailHeaderLbl.textColor = UIColor.UCAColor.AppThemeColor_1
        
        emailHeaderLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_VERIFy_TXT")
        emailSentLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_SENT_TO")
        
        smsHelpLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_SMS_SENT_NOTE")
        emailHelpLbl.text = generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_SENT_NOTE")
        
        self.smsHelpLbl.fitText()
        self.emailHelpLbl.fitText()
        
        self.smsResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_SMS"))
        self.smsMobileEditBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_MOBILE"))
        self.smsOkBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"))
        self.emailOkBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"))
        self.emailResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_EMAIL"))
        self.emailEditBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_EMAIL"))
        
        self.smsResendBtn.clickDelegate = self
        self.smsMobileEditBtn.clickDelegate = self
        self.smsOkBtn.clickDelegate = self
        self.emailOkBtn.clickDelegate = self
        self.emailResendBtn.clickDelegate = self
        self.emailEditBtn.clickDelegate = self
        
        if(isEditProfile || isSignUpPage || isAccountInfo){
            smsMobileNumLbl.text = "+" + mobileNum
        }else{
            setUserData()
        }
        
        if(requestType.trim() == "DO_PHONE_VERIFY"){
            emailViewHeight.constant = 0
            emailView.isHidden = true
            
            emailIdVerified = true
            
            self.emailViewHeight.constant = 0
        }else if(requestType.trim() == "DO_EMAIL_VERIFY"){
            smsViewHeight.constant = 0
            smsView.isHidden = true
            
            mobileNumVerified = true
            
            self.smsViewHeight.constant = 0
        }
        requestVerification()
        
        demoHintLbl.text = "Note: Please enter the OTP \"12345\" If you do not receive SMS/EMAIL on your registered number in next one minute. "
        demoHintLbl.backgroundColor = UIColor(hex: 0x4cb74c)
        demoHintLbl.textColor = UIColor.white
        demoHintLbl.fitText()
        
        
        if(GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as! String).uppercased() == "DEMO"){
            demoHintLbl.isHidden = false
        }else{
            demoHintLbl.isHidden = true
            self.demoHintLblHeight.constant = 0
        }
        
        emailView.layer.shadowOpacity = 0.5
        emailView.layer.shadowOffset = CGSize(width: 0, height: 3)
        emailView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        smsView.layer.shadowOpacity = 0.5
        smsView.layer.shadowOffset = CGSize(width: 0, height: 3)
        smsView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
    }
    
    func setUserData(){
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        mobileNum = userProfileJson.get("vCode") + userProfileJson.get("vPhone")
        

        let ePhoneVerified = userProfileJson.get("ePhoneVerified")
        let eEmailVerified = userProfileJson.get("eEmailVerified")
        
        if(isEditInfoTapped == true){
            if((smsMobileNumLbl.text! != "+\(mobileNum)" && ePhoneVerified.uppercased() != "YES") && (emailIdLbl.text! != userProfileJson.get("vEmail") && eEmailVerified.uppercased() != "YES")){
                requestType = "DO_EMAIL_PHONE_VERIFY"
                self.smsView.isHidden = false
                self.emailView.isHidden = false
                
                self.emailIdVerified = false
                self.mobileNumVerified = false
                
                emailVerificationCode = ""
                smsVerificationCode = ""
                
                self.setScreenHeight()
                requestVerification()
            }else if(smsMobileNumLbl.text! != "+\(mobileNum)" && ePhoneVerified.uppercased() != "YES"){
                requestType = "DO_PHONE_VERIFY"
                self.smsView.isHidden = false
                
                smsVerificationCode = ""
                
                self.mobileNumVerified = false
                
                self.setScreenHeight()
                requestVerification()
            }else if(emailIdLbl.text! != userProfileJson.get("vEmail") && eEmailVerified.uppercased() != "YES"){
                requestType = "DO_EMAIL_VERIFY"
                
                emailVerificationCode = ""
                
                self.emailIdVerified = false
                
                self.emailView.isHidden = false
                self.setScreenHeight()
                requestVerification()
            }else{
                self.smsView.isHidden = true
                self.emailView.isHidden = true
                
                self.closeCurrentScreen()
            }
            
            isEditInfoTapped = false
        }
        
        
        if(smsMobileNumLbl != nil){
            smsMobileNumLbl.text = "+" + mobileNum
        }
        
        if(emailIdLbl != nil){
            emailIdLbl.text = userProfileJson.get("vEmail")
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        self.view.endEditing(true)
        if(sender == smsResendBtn){
            requestType = "DO_PHONE_VERIFY"
            requestVerification()
        }else if(sender == smsMobileEditBtn){
            
            isEditInfoTapped = true
            
            if(mainScreenUv !=  nil || menuScreenUv != nil){
                
                let manageProfileUv = GeneralFunctions.instantiateViewController(pageName: "ManageProfileUV") as! ManageProfileUV
                manageProfileUv.isOpenEditProfile = true
                manageProfileUv.isFromAccountVerifyScreen = true
                //        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageProfileUv, animated: true)
                self.pushToNavController(uv: manageProfileUv)
            }else{
                self.closeCurrentScreen()
            }
        }else if(sender == smsOkBtn){
             let smsCode_str = Utils.getText(textField: smsTxtField.getTextField()!)
            if((smsCode_str == smsVerificationCode && smsVerificationCode != "") || ((GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as! String).uppercased() == "DEMO" && smsCode_str == "12345")){
                requestType = "PHONE_VERIFIED"
                requestVerification()
            }else{
                
                if(smsCode_str == ""){
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ENTER_VERIFICATION_CODE"))
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_VERIFICATION_CODE_INVALID"))
                }
                
            }
            
        }else if(sender == emailOkBtn){
            
            let emailCode_str = Utils.getText(textField: emailTxtField.getTextField()!)
            //        emailCode_str == "12345" ||
            if((emailCode_str == emailVerificationCode && emailVerificationCode != "") || ((GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as! String).uppercased() == "DEMO" && emailCode_str == "12345")){
                requestType = "EMAIL_VERIFIED"
                requestVerification()
            }else{
                
                if(emailCode_str == ""){
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ENTER_VERIFICATION_CODE"))
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_VERIFICATION_CODE_INVALID"))
                }
            }
            
        }else if(sender == emailResendBtn){
            requestType = "DO_EMAIL_VERIFY"
            requestVerification()
        }else if(sender == emailEditBtn){
            
            isEditInfoTapped = true
            
            if(mainScreenUv !=  nil || menuScreenUv != nil){
                
                let manageProfileUv = GeneralFunctions.instantiateViewController(pageName: "ManageProfileUV") as! ManageProfileUV
                manageProfileUv.isOpenEditProfile = true
                manageProfileUv.isFromAccountVerifyScreen = true
                //        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageProfileUv, animated: true)
                self.pushToNavController(uv: manageProfileUv)
            }else{
                self.closeCurrentScreen()
            }
        }
    }
    
    func requestVerification(){
        
        if(requestType == "DO_EMAIL_PHONE_VERIFY"){
            DispatchQueue.main.async {
                self.emailResendBtn.setButtonEnabled(isBtnEnabled: false)
                self.smsResendBtn.setButtonEnabled(isBtnEnabled: false)
                
                self.emailResendBtn.setButtonTitleColor(color: UIColor(hex: 0x6b6b6b))
                self.smsResendBtn.setButtonTitleColor(color: UIColor(hex: 0x6b6b6b))
                
            }
        }else if(requestType == "DO_EMAIL_VERIFY"){
            DispatchQueue.main.async {
                self.emailResendBtn.setButtonEnabled(isBtnEnabled: false)
                self.emailResendBtn.setButtonTitleColor(color: UIColor(hex: 0x6b6b6b))
            }
        }else if(requestType == "DO_PHONE_VERIFY"){
            DispatchQueue.main.async {
                self.smsResendBtn.setButtonEnabled(isBtnEnabled: false)
                self.smsResendBtn.setButtonTitleColor(color: UIColor(hex: 0x6b6b6b))
            }
        }
        
        let parameters = ["type":"sendVerificationSMS", "iMemberId": GeneralFunctions.getMemberd(), "MobileNo": mobileNum, "UserType": Utils.appUserType, "REQ_TYPE": requestType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.resetConfiguration(responsepositive:true)
                    self.checkResponse(dataDict)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    self.resetConfiguration(responsepositive:false)
                }
                
            }else{
                self.generalFunc.setError(uv: self)
                self.resetConfiguration(responsepositive:false)
            }
        })
    }
    
    func resetConfiguration(responsepositive:Bool){
        
        VERIFICATION_CODE_RESEND_TIME_IN_SECONDS =  GeneralFunctions.parseInt(origValue: 0, data: userProfileJson.get("VERIFICATION_CODE_RESEND_TIME_IN_SECONDS"))
        
    	if(requestType == "DO_EMAIL_PHONE_VERIFY"){
            
            if isFirstVerification == false && responsepositive == true
            {
                self.smsResendBtn.setButtonTitle(buttonTitle: self.timeString(time: TimeInterval(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS)))
                self.smsResendBtn.setButtonTitle(buttonTitle: String(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS))
                Timer.scheduledTimer(timeInterval: TimeInterval(1), target: self, selector: #selector(callTimer), userInfo: "DO_EMAIL_PHONE_VERIFY", repeats: true)
                
                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(Double(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS) * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                    
                    self.smsResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_SMS"))
                    
                    self.emailResendBtn.setButtonEnabled(isBtnEnabled: true)
                    self.smsResendBtn.setButtonEnabled(isBtnEnabled: true)
                    
                    self.emailResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                    self.smsResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                })
            }
            else
            {
                isFirstVerification = false
                self.smsResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                self.smsResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_SMS"))
                self.smsResendBtn.setButtonEnabled(isBtnEnabled: true)
                
                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(Double(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS) * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                    
                    self.emailResendBtn.setButtonEnabled(isBtnEnabled: true)
                    self.emailResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                    
                })
                
            }
            
        }else if(requestType == "DO_EMAIL_VERIFY"){
            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(30 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                self.emailResendBtn.setButtonEnabled(isBtnEnabled: true)
                self.emailResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
            })
        }else if(requestType == "DO_PHONE_VERIFY"){
            
            if isFirstVerification == false && responsepositive == true
            {
                self.smsResendBtn.setButtonTitle(buttonTitle: self.timeString(time: TimeInterval(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS)))
                self.smsResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                Timer.scheduledTimer(timeInterval: TimeInterval(1), target: self, selector: #selector(callTimer), userInfo: "DO_PHONE_VERIFY", repeats: true)
                
                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(Double(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS) * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                    
                    self.smsResendBtn.setButtonEnabled(isBtnEnabled: true)
                    self.smsResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                })
                
            }else
            {
                self.isFirstVerification = false
                self.smsResendBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor)
                self.smsResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_SMS"))
                self.smsResendBtn.setButtonEnabled(isBtnEnabled: true)
                
            }
            
        }
    }
    
    func callTimer(timer:Timer)
    {
        if timer.userInfo as! String == "DO_PHONE_VERIFY" || timer.userInfo as! String == "DO_EMAIL_PHONE_VERIFY"
        {
            
            VERIFICATION_CODE_RESEND_TIME_IN_SECONDS = VERIFICATION_CODE_RESEND_TIME_IN_SECONDS - 1
            
            VERIFICATION_CODE_RESEND_RUNNING = true
            
            self.smsResendBtn.setButtonTitle(buttonTitle: self.timeString(time: TimeInterval(VERIFICATION_CODE_RESEND_TIME_IN_SECONDS)))
            if VERIFICATION_CODE_RESEND_TIME_IN_SECONDS == 0
            {
                VERIFICATION_CODE_RESEND_RUNNING = false
                
                self.smsResendBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RESEND_SMS"))
                timer.invalidate()
            }
            
        }
        
    }
    func timeString(time:TimeInterval) -> String
    {
        let min = Configurations.convertNumToAppLocal(numStr:String(format:"%02i",Int(time) / 60 % 60))
        let sec = Configurations.convertNumToAppLocal(numStr:String(format:"%02i",Int(time) % 60))
        return "\(min):\(sec)"
    }
    
    func checkResponse(_ dict:NSDictionary){
        
        Utils.printLog(msgData: "VerificationResponse:\(dict)")
        
        let action_str=dict.get("Action")
        
        if(action_str == "1"){
            if(requestType == "DO_EMAIL_PHONE_VERIFY"){
                
//                let message = dict.get("message")
//                if(message != nil){
                    let message_str = dict.get("message")
                    if(message_str != ""){
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: message_str))
                    }else {
                        let msg_sms = dict.get("message_sms")
                        let msg_email = dict.get("message_email")
                        
                        if(msg_sms == "LBL_MOBILE_VERIFICATION_FAILED_TXT" && msg_email == "LBL_EMAIL_VERIFICATION_FAILED_TXT"){
                            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACC_VERIFICATION_FAILED"))
                            return
                        }
                        if(message_str != "LBL_MOBILE_VERIFICATION_FAILED_TXT" && msg_sms != "LBL_MOBILE_VERIFICATION_FAILED_TXT"){
                            smsVerificationCode = dict.get("message_sms")
                        }else{
                            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: msg_sms))
                        }
                        
                        if(message_str != "LBL_EMAIL_VERIFICATION_FAILED_TXT" && msg_email != "LBL_EMAIL_VERIFICATION_FAILED_TXT"){
                            emailVerificationCode = dict.get("message_email")
                        }else{
                            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: msg_email))
                        }
                    }
//                }
            }else if(requestType == "DO_EMAIL_VERIFY"){
                emailVerificationCode = dict.get("message")
            }else if(requestType == "DO_PHONE_VERIFY"){
                smsVerificationCode = dict.get("message")
            }else if(requestType == "PHONE_VERIFIED"){
                DispatchQueue.main.async {
                    
                    self.mobileNumVerified = true
                    
                    
                    let userDetails = dict.getObj("userDetails")
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: userDetails.convertToJson() as AnyObject)
                    
                    if(self.emailIdVerified == true){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            if(btnClickedId == 0){
                                if(self.isSignUpPage == true){
                                    self.performSegue(withIdentifier: "unwindToSignUp", sender: self)
                                }else if(self.isEditProfile == true){
                                    self.performSegue(withIdentifier: "unwindToEditProfile", sender: self)
                                }else if(self.isAccountInfo == true){
                                    self.performSegue(withIdentifier: "unwindToAccountInfo", sender: self)
                                }else if(self.isMainPage == true){
                                    self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                                }else{
                                    self.closeCurrentScreen()
                                }
                            }
                        })
                    }else{
                        self.smsView.isHidden = true
                        self.smsViewHeight.constant = 0
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dict.get("message")))
                    }
                }
                
            }else if(requestType == "EMAIL_VERIFIED"){
                DispatchQueue.main.async {
                    
                    self.emailIdVerified = true
                    
                    let userDetails = dict.getObj("userDetails")
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: userDetails.convertToJson() as AnyObject)

                    
                    if(self.mobileNumVerified == true){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            if(btnClickedId == 0){
                                
                                if(self.isSignUpPage == true){
                                    self.performSegue(withIdentifier: "unwindToSignUp", sender: self)
                                }else if(self.isEditProfile == true){
                                    self.performSegue(withIdentifier: "unwindToEditProfile", sender: self)
                                }else if(self.isAccountInfo == true){
                                    self.performSegue(withIdentifier: "unwindToAccountInfo", sender: self)
                                }else if(self.isMainPage == true){
                                    self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                                }else{
                                    self.closeCurrentScreen()
                                }
                                
                            }
                        })
                    }else{
                        self.emailView.isHidden = true
                        self.emailViewHeight.constant = 0
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dict.get("message")))
                    }
                }
                //                self.buildErrorMsg("", content: self.getLabelValue((dict.valueForKey("message") as! String)))
            }
        }else{
            self.generalFunc.setError(uv: self)
        }
        
    }

    
    @IBAction func unwindToAccountVerificationScreen(_ segue:UIStoryboardSegue) {
        //        unwindToSignUp
        
        if(segue.source.isKind(of: ManageProfileUV.self)){
            if(isFirstLaunch == false && (mainScreenUv != nil || menuScreenUv != nil)){
                setUserData()
            }
        }
    }
}
