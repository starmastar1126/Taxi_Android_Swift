//
//  SignUpUV.swift
//  DriverApp
//
//  Created by NEW MAC on 06/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class SignUpUV: UIViewController, MyLabelClickDelegate, MyBtnClickDelegate, MyTxtFieldClickDelegate {
    
    @IBOutlet weak var socialLoginStkView: UIStackView!
    @IBOutlet weak var googleImgView: UIImageView!
    @IBOutlet weak var twitterImgView: UIImageView!
    @IBOutlet weak var fbImgView: UIImageView!
    @IBOutlet weak var scrollContentView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var signUpHLbl: MyLabel!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var orLblContainer: UIView!
    @IBOutlet weak var orLbl: MyLabel!
    @IBOutlet weak var goSignInLbl: MyLabel!
    @IBOutlet weak var goToSignInContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var goToSignInContainerView: UIView!
    
    @IBOutlet weak var termsLbl: MyLabel!
    @IBOutlet weak var termsCheckBox: BEMCheckBox!
    @IBOutlet weak var fNameTxtField: MyTextField!
    @IBOutlet weak var lNameTxtField: MyTextField!
    @IBOutlet weak var emailTxtField: MyTextField!
    @IBOutlet weak var countryTxtField: MyTextField!
    @IBOutlet weak var mobileNumTxtField: MyTextField!
    @IBOutlet weak var passwordTxtField: MyTextField!
    @IBOutlet weak var inviteCodeAreaHeight: NSLayoutConstraint!
    @IBOutlet weak var inviteCodeAreaView: UIView!
    @IBOutlet weak var inviteTxtField: MyTextField!
    @IBOutlet weak var helpImgView: UIImageView!
    @IBOutlet weak var registerBtn: MyButton!
    
    var isFromAppLogin = true
    
    let generalFunc = GeneralFunctions()
    
    var required_str = ""
    
    var isCountrySelected = false
    var selectedCountryCode = ""
    var selectedPhoneCode = ""
    
    
    var openFbLogin: OpenFbLogin!
    var openGoogleLogin: OpenGoogleLogin!
    var openTwitterLogin: OpenTwitterLogin!
    
    var isFirstLaunch = true
//    var isViewProperShutDown = false
    
    var isBottomViewSet = false
    
    var cntView:UIView!
    var previousNextView:IQPreviousNextView!
    
    var isSafeAreaSet = false
    
    var PAGE_HEIGHT:CGFloat = 715
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "SignUpScreenDesign", uv: self, contentView: contentView))
        cntView = self.generalFunc.loadView(nibName: "SignUpScreenDesign", uv: self, contentView: scrollView)
        cntView.frame.size.width = Application.screenSize.width
        
        previousNextView = IQPreviousNextView(frame: cntView.frame)
        previousNextView.addSubview(cntView)
        previousNextView.center = cntView.center
        
        self.scrollView.addSubview(previousNextView)
        self.scrollView.bounces = false
        
        self.addBackBarBtn()
        
        let facebookLoginEnabled = GeneralFunctions.getValue(key: Utils.FACEBOOK_LOGIN_KEY)
        let googleLoginEnabled = GeneralFunctions.getValue(key: Utils.GOOGLE_LOGIN_KEY)
        let twitterLoginEnabled = GeneralFunctions.getValue(key: Utils.TWITTER_LOGIN_KEY)
        
        var isFBLoginEnabled = false
        var isGoogleLoginEnabled = false
        var isTwitterLoginEnabled = false
        if(facebookLoginEnabled != nil && (facebookLoginEnabled as! String).uppercased() == "NO"){
            isFBLoginEnabled = false
            self.fbImgView.isHidden = true
        }else{
            isFBLoginEnabled = true
        }
        
        if(googleLoginEnabled != nil && (googleLoginEnabled as! String).uppercased() == "NO"){
            isGoogleLoginEnabled = false
            self.googleImgView.isHidden = true
        }else{
            isGoogleLoginEnabled = true
        }
        
        if(twitterLoginEnabled != nil && (twitterLoginEnabled as! String).uppercased() == "NO"){
            isTwitterLoginEnabled = false
            self.twitterImgView.isHidden = true
        }else{
            isTwitterLoginEnabled = true
        }
        
        if(isFBLoginEnabled == false && isGoogleLoginEnabled == false && isTwitterLoginEnabled == false){
            for i in 0..<socialLoginStkView.subviews.count{
                let subView = socialLoginStkView.subviews[i]
                
                subView.isHidden = true
            }
            
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 180
        }
        
        setData()
        
    }
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidAppear(_ animated: Bool) {
        self.emailTxtField.getTextField()!.keyboardType = .emailAddress
        self.passwordTxtField.getTextField()!.isSecureTextEntry = true
        self.mobileNumTxtField.getTextField()!.keyboardType = .numberPad
        
        
        if(isFirstLaunch){
            countryTxtField.addArrowView(color: UIColor(hex: 0xbfbfbf), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
            
//            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.registerBtn.frame.maxY + 20)
            
            
            if(PAGE_HEIGHT < self.cntView.frame.height){
                self.PAGE_HEIGHT = self.cntView.frame.height
            }
            self.cntView.frame.size.height = self.PAGE_HEIGHT
            self.previousNextView.frame.size.height = self.PAGE_HEIGHT

            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
                        
            isFirstLaunch = false
        }
//        if (isViewProperShutDown == true)
//        {
//            fNameTxtField.setText(text: "")
//            lNameTxtField.setText(text: "")
//            emailTxtField.setText(text: "")
//            countryTxtField.setText(text: "")
//            mobileNumTxtField.setText(text: "")
//            passwordTxtField.setText(text: "")
//            inviteTxtField.setText(text: "")
//            isViewProperShutDown = false
//        }
    }
    
    
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            
            self.goToSignInContainerViewHeight.constant = 50 + GeneralFunctions.getSafeAreaInsets().bottom
            isSafeAreaSet = true
        }
    }
    
    func setData(){
        required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        self.orLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_OR_TXT")
        
        
        if(GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) as! String).uppercased() == "YES"){
            self.registerBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_NEXT_TXT"))
        }else{
            self.registerBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_REGISTER_TXT"))
        }
        
        self.title = self.generalFunc.getLanguageLabel(origValue: "SignUp", key: "LBL_SIGN_UP")
        
        
        self.orLblContainer.transform = CGAffineTransform(rotationAngle: 45 * CGFloat(CGFloat.pi/180))
        self.orLbl.transform = CGAffineTransform(rotationAngle: -45 * CGFloat(CGFloat.pi/180))
        
        self.goSignInLbl.setClickDelegate(clickDelegate: self)
        self.registerBtn.clickDelegate = self
        
        self.fNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FIRST_NAME_HEADER_TXT"))
        self.lNameTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LAST_NAME_HEADER_TXT"))
        self.emailTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMAIL_LBL_TXT"))
        self.countryTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COUNTRY_TXT"))
        self.mobileNumTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MOBILE_NUMBER_HEADER_TXT"))
        self.passwordTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PASSWORD_LBL_TXT"))
        self.inviteTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_CODE_HINT"))
        
        if(GeneralFunctions.getValue(key: Utils.REFERRAL_SCHEME_ENABLE) != nil && (GeneralFunctions.getValue(key: Utils.REFERRAL_SCHEME_ENABLE) as! String).uppercased() != "YES" ){
            
            self.inviteCodeAreaView.isHidden = true
            self.inviteCodeAreaHeight.constant = 0
            
        }else{
            //            self.scrollView.setContentViewSize(offset: 15)
        }
        
        let inviteHelpTapGue = UITapGestureRecognizer()
        
        inviteHelpTapGue.addTarget(self, action: #selector(self.inviteHelpImgTapped(sender:)))
        self.helpImgView.isUserInteractionEnabled = true
        self.helpImgView.addGestureRecognizer(inviteHelpTapGue)
        
        self.countryTxtField.setEnable(isEnabled: false)
        self.countryTxtField.myTxtFieldDelegate = self
        
        self.mobileNumTxtField.getTextField()!.keyboardType = .numberPad
        self.emailTxtField.getTextField()!.keyboardType = .emailAddress
        self.passwordTxtField.getTextField()!.isSecureTextEntry = true
        
        signUpHLbl.text = self.generalFunc.getLanguageLabel(origValue: "SIGN UP WITH SOCIAL ACCOUNTS", key: "LBL_SIGN_UP_WITH_SOC_ACC")
        
        self.goSignInLbl.text = self.generalFunc.getLanguageLabel(origValue: "Already have an account?", key: "LBL_ALREADY_HAVE_ACC")
        
        
        self.goSignInLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        self.goToSignInContainerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        fbImgView.isUserInteractionEnabled = true
        googleImgView.isUserInteractionEnabled = true
        twitterImgView.isUserInteractionEnabled = true
        
        let fbImgTapGue = UITapGestureRecognizer()
        let googleImgTapGue = UITapGestureRecognizer()
        let twittImgTapGue = UITapGestureRecognizer()
        
        fbImgTapGue.addTarget(self, action: #selector(self.fbBtnTapped))
        googleImgTapGue.addTarget(self, action: #selector(self.googleBtnTapped))
        twittImgTapGue.addTarget(self, action: #selector(self.twittBtnTapped))
        
        fbImgView.addGestureRecognizer(fbImgTapGue)
        googleImgView.addGestureRecognizer(googleImgTapGue)
        twitterImgView.addGestureRecognizer(twittImgTapGue)
        //        For Terms and Conditions
        
        self.termsCheckBox.boxType = .square
        self.termsCheckBox.offAnimationType = .bounce
        self.termsCheckBox.onAnimationType = .bounce
        self.termsCheckBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
        self.termsCheckBox.onFillColor = UIColor.UCAColor.AppThemeColor
        self.termsCheckBox.onTintColor = UIColor.UCAColor.AppThemeColor
        self.termsCheckBox.tintColor = UIColor.UCAColor.AppThemeColor_1
        
        var multipleAttributes = [String : Any]()
        multipleAttributes[NSForegroundColorAttributeName] = UIColor.UCAColor.AppThemeColor
        multipleAttributes[NSUnderlineStyleAttributeName] = NSUnderlineStyle.styleSingle.rawValue
        
        let attrString1 = NSMutableAttributedString(string: self.generalFunc.getLanguageLabel(origValue: "I agree to the", key: "LBL_TERMS_CONDITION_PREFIX") + " ")
        let attrString2 = NSMutableAttributedString(string: self.generalFunc.getLanguageLabel(origValue: "Terms & Conditions and Privacy Policy", key: "LBL_TERMS_PRIVACY"))
        
        attrString2.addAttributes(multipleAttributes, range: NSMakeRange(0, attrString2.length))
        attrString1.append(attrString2)
        
        self.termsLbl.attributedText = attrString1
        self.termsLbl.setClickDelegate(clickDelegate: self)
        self.termsLbl.fitText()
        
        if(GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_KEY) as! String) != "" && GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) as! String) != "" && GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) as! String) != ""){
            self.selectedCountryCode = (GeneralFunctions.getValue(key: Utils.DEFAULT_COUNTRY_CODE_KEY) as! String)
            self.selectedPhoneCode = (GeneralFunctions.getValue(key: Utils.DEFAULT_PHONE_CODE_KEY) as! String)
            //            self.countryTxtField.getTextField()!.text = "+" + selectedPhoneCode
            
            self.countryTxtField.setText(text: "+" + self.selectedPhoneCode)
            
            self.isCountrySelected = true
            self.countryTxtField.getTextField()!.sendActions(for: .editingChanged)
        }
    }
    
//    override func viewDidLayoutSubviews() {
//        if(isBottomViewSet == false){
//            //            Utils.printLog(msgData: "HEIGHT:\(Application.screenSize.height):Y:\(self.registerBtn.frame.maxY)")
//            if(UIDevice().type == .iPhoneX || (UIDevice().type == .simulator && Application.screenSize.height == 812)){
//                let tempView = UIView()
//                tempView.frame = CGRect(x: 0, y: self.registerBtn.frame.maxY, width: Application.screenSize.width, height: 42)
//                tempView.backgroundColor = UIColor.UCAColor.AppThemeColor
//                self.view.addSubview(tempView)
//            }
//            isBottomViewSet = true
//        }
//    }
    
    func inviteHelpImgTapped(sender:UITapGestureRecognizer){
        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_REFERAL_SCHEME"))
    }
    
    func fbBtnTapped(){
//        isViewProperShutDown = true
        let window = UIApplication.shared.delegate!.window!
        
        openFbLogin = OpenFbLogin(uv: self, window: window!)
        
        openFbLogin.processData(openFbLoginInst: openFbLogin)
    }
    
    func googleBtnTapped(){
//        isViewProperShutDown = true
        let window = UIApplication.shared.delegate!.window!
        
        openGoogleLogin = OpenGoogleLogin(uv: self, window: window!)
        openGoogleLogin.processData(currGoogleLoginInst: openGoogleLogin)
        
    }
    func twittBtnTapped(){
//        isViewProperShutDown = true
        let window = UIApplication.shared.delegate!.window!
        
        openTwitterLogin = OpenTwitterLogin(uv: self, window: window!)
        openTwitterLogin.processData(currTwitterLoginInst: openTwitterLogin)
        
    }
    
    func myTxtFieldTapped(sender: MyTextField) {
        
        if(sender == self.countryTxtField){
            let countryListUv = GeneralFunctions.instantiateViewController(pageName: "CountryListUV") as! CountryListUV
            countryListUv.fromRegister = true
            self.pushToNavController(uv: countryListUv)
        }
    }
    
    func myLableTapped(sender: MyLabel) {
        
        
        if(sender == goSignInLbl){
//            isViewProperShutDown = true
            if(isFromAppLogin){
                let signInUv = GeneralFunctions.instantiateViewController(pageName: "SignInUV") as! SignInUV
                signInUv.isFromAppLogin = false
                self.pushToNavController(uv: signInUv)
            }else{
                self.closeCurrentScreen()
            }
        }
        else if(sender == termsLbl)
        {
            let supportUv = GeneralFunctions.instantiateViewController(pageName: "SupportUV") as! SupportUV
            supportUv.isOnlyPrivacyAndTerms = true
            self.pushToNavController(uv: supportUv)
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.registerBtn){
            checkData()
        }
    }
    
    func checkData(){
        let noWhiteSpace = generalFunc.getLanguageLabel(origValue: "Password should not contain whitespace.", key: "LBL_ERROR_NO_SPACE_IN_PASS");
        let pass_length = generalFunc.getLanguageLabel(origValue: "Password must be", key: "LBL_ERROR_PASS_LENGTH_PREFIX")
            + " \(Utils.minPasswordLength)"  + generalFunc.getLanguageLabel(origValue: "or more character long.",key: "LBL_ERROR_PASS_LENGTH_SUFFIX")
        let mobileInvalid = generalFunc.getLanguageLabel(origValue: "Invalid mobile no.", key: "LBL_INVALID_MOBILE_NO")
        
        let fNameEntered = Utils.checkText(textField: self.fNameTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.fNameTxtField.getTextField()!, error: required_str)
        let lNameEntered = Utils.checkText(textField: self.lNameTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.lNameTxtField.getTextField()!, error: required_str)
        let emailEntered = Utils.checkText(textField: self.emailTxtField.getTextField()!) ? (GeneralFunctions.isValidEmail(testStr: Utils.getText(textField: self.emailTxtField.getTextField()!)) ? true : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_EMAIL_ERROR_TXT"))) : Utils.setErrorFields(textField: self.emailTxtField.getTextField()!, error: required_str)
        //        let mobileEntered = Utils.checkText(textField: self.mobileNumTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.mobileNumTxtField.getTextField()!, error: required_str)
        let mobileEntered = Utils.checkText(textField: self.mobileNumTxtField.getTextField()!) ? (Utils.getText(textField: self.mobileNumTxtField.getTextField()!).characters.count >= Utils.minMobileLength ? true : Utils.setErrorFields(textField: self.mobileNumTxtField.getTextField()!, error: mobileInvalid)) : Utils.setErrorFields(textField: self.mobileNumTxtField.getTextField()!, error: required_str)
        let passwordEntered = Utils.checkText(textField: self.passwordTxtField.getTextField()!) ? (Utils.getText(textField: self.passwordTxtField.getTextField()!).contains(" ") ? Utils.setErrorFields(textField: self.passwordTxtField.getTextField()!, error: noWhiteSpace) : (Utils.getText(textField: self.passwordTxtField.getTextField()!).characters.count >= Utils.minPasswordLength ? true : Utils.setErrorFields(textField: self.passwordTxtField.getTextField()!, error: pass_length))) : Utils.setErrorFields(textField: self.passwordTxtField.getTextField()!, error: required_str)
        let countryEntered = isCountrySelected ? true : Utils.setErrorFields(textField: self.countryTxtField.getTextField()!, error: required_str)
        
        if (fNameEntered == false || lNameEntered == false || emailEntered == false || mobileEntered == false
            || countryEntered == false || passwordEntered == false) {
            return;
        }
        if (termsCheckBox.on == false){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Please accept our Terms & Condition and Privacy Policy", key: "LBL_ACCEPT_TERMS_PRIVACY_ALERT"))
            return;
        }
        if(GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) != nil && (GeneralFunctions.getValue(key: Utils.MOBILE_VERIFICATION_ENABLE_KEY) as! String).uppercased() == "YES"){
            checkUserExist()
        }else{
            registerUser()
        }
    }
    
    func checkUserExist(){
        let parameters = ["type":"isUserExist","Email": Utils.getText(textField: self.emailTxtField.getTextField()!), "Phone": Utils.getText(textField: self.mobileNumTxtField.getTextField()!), "PhoneCode": self.selectedPhoneCode]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    DispatchQueue.main.async() {
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_VERIFY_MOBILE_CONFIRM_MSG"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "OK", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 0){
                                let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
                                accountVerificationUv.mobileNum = self.selectedPhoneCode + Utils.getText(textField: self.mobileNumTxtField.getTextField()!)
                                accountVerificationUv.isSignUpPage = true
                                accountVerificationUv.requestType = "DO_PHONE_VERIFY"
                                self.pushToNavController(uv: accountVerificationUv)
                            }
                        })
                    }
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func registerUser(){
        let userSelectedCurrency = GeneralFunctions.getValue(key: Utils.DEFAULT_CURRENCY_TITLE_KEY) as! String
        let userSelectedLanguage = GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String
        
        let parameters = ["type":"signup","vFirstName": Utils.getText(textField: self.fNameTxtField.getTextField()!), "vLastName": Utils.getText(textField: self.lNameTxtField.getTextField()!), "vEmail": Utils.getText(textField: self.emailTxtField.getTextField()!), "vPhone": Utils.getText(textField: self.mobileNumTxtField.getTextField()!), "vPassword": Utils.getText(textField: self.passwordTxtField.getTextField()!), "PhoneCode": self.selectedPhoneCode, "CountryCode": self.selectedCountryCode, "vDeviceType": Utils.deviceType, "vInviteCode": Utils.getText(textField: self.inviteTxtField.getTextField()!), "vCurrency": userSelectedCurrency, "vLang": userSelectedLanguage, "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    _ = SetUserData(uv: self, userProfileJson: dataDict, isStoreUserId: true)
                    
                    let window = UIApplication.shared.delegate!.window!
                    _ = OpenMainProfile(uv: self, userProfileJson: response, window: window!)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    @IBAction func unwindToSignUp(_ segue:UIStoryboardSegue) {
        
        if(segue.source .isKind(of: CountryListUV.self))
        {
            
            let sourceViewController = segue.source as? CountryListUV
            let selectedPhoneCode:String = sourceViewController!.selectedCountryHolder!.vPhoneCode
            let selectedCountryCode = sourceViewController!.selectedCountryHolder!.vCountryCode
            
            self.selectedCountryCode = selectedCountryCode
            self.selectedPhoneCode = selectedPhoneCode
            self.countryTxtField.setText(text: "+" + selectedPhoneCode)
            self.isCountrySelected = true
            self.countryTxtField.getTextField()!.sendActions(for: .editingChanged)
            
        }else if(segue.source.isKind(of: AccountVerificationUV.self)){
            let accountVerificationUv = segue.source as! AccountVerificationUV
            
            if(accountVerificationUv.mobileNumVerified == true){
                registerUser()
            }
        }
        
    }
}
