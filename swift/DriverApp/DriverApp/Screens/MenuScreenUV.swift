//
//  MenuScreenUV.swift
//  DriverApp
//
//  Created by NEW MAC on 12/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class MenuScreenUV: UIViewController, UITableViewDelegate, UITableViewDataSource, NavigationDrawerControllerDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var userDataContainerView: UIView!
    @IBOutlet weak var settingsImgView: UIImageView!
    @IBOutlet weak var menuUserAreaBgImgView: UIImageView!
    @IBOutlet weak var userDataInsideTopMargin: NSLayoutConstraint!
    @IBOutlet weak var userDataContainerViewHeight: NSLayoutConstraint!
    
    @IBOutlet weak var walletAmountUpdateActIndicatorContainerView: UIView!
    @IBOutlet weak var walletAmountUpdateActIndicator: UIActivityIndicatorView!
    
    @IBOutlet weak var usrProfileImgView: UIImageView!
    @IBOutlet weak var logOutView: UIView!
    @IBOutlet weak var logOutLbl: MyLabel!
    @IBOutlet weak var logOutImgView: UIImageView!
    @IBOutlet weak var logOutViewHeight: NSLayoutConstraint!
    
    @IBOutlet weak var userHeaderName: MyLabel!
    
    @IBOutlet weak var listContainerView: UIView!
    @IBOutlet weak var walletHLbl: MyLabel!
    @IBOutlet weak var walletVLbl: MyLabel!
    
    //GenderView related OutLets
    @IBOutlet weak var genderVCloseImgView: UIImageView!
    @IBOutlet weak var genderHLbl: MyLabel!
    @IBOutlet weak var maleImgView: UIImageView!
    @IBOutlet weak var femaleImgView: UIImageView!
    @IBOutlet weak var maleLbl: MyLabel!
    @IBOutlet weak var femaleLbl: MyLabel!
    
    //DynamicConstraints related OutLets
//    @IBOutlet weak var userDataContainerViewHeightConstraint: NSLayoutConstraint!
//    @IBOutlet weak var usrProfileImgViewTopConstraint: NSLayoutConstraint!
//    @IBOutlet weak var settingsImgViewTopConstraint: NSLayoutConstraint!
    
    //    var mainScreenUv:MainScreenUV!
    
    var MENU_PROFILE = "0"
    var MENU_PAYMENT = "1"
    var MENU_WALLET = "2"
    var MENU_INVITE_FRIENDS = "3"
    var MENU_RIDE_HISTORY = "4"
    var MENU_BOOKINGS = "5"
    var MENU_ABOUTUS = "6"
    var MENU_CONTACTUS = "7"
    var MENU_HELP = "8"
    var MENU_EMERGENCY = "9"
    var MENU_SIGN_OUT = "10"
    var MENU_PRIVACY = "11"
    var MENU_SUPPORT = "12"
    var MENU_DRIVER_FEEDBACK = "13"
    var MENU_MANAGE_VEHICLES = "14"
    var MENU_TRIP_STATISTICS = "15"
    var MENU_YOUR_DOC = "16"
    var MENU_HEAT_VIEW = "17"
    var MENU_ACCOUNT_VERIFY = "18"
    var MENU_BANK_DETAILS = "19"
    var MENU_MY_AVAIL = "20"
    
    var genderView:UIView!
    
    var items = [NSDictionary]()
    
    let generalFunc = GeneralFunctions()
    
    var userProfileJson:NSDictionary!
    
    var isBottomViewSet = false

    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "MenuScreenDesign", uv: self, contentView: contentView))
        
        self.tableView.tableFooterView = UIView(frame: CGRect.zero)
        
        Utils.createRoundedView(view: usrProfileImgView, borderColor:  UIColor.UCAColor.AppThemeColor, borderWidth: 0.0)
        
        self.listContainerView.backgroundColor = UIColor.UCAColor.AppThemeColor_Dark
        
        self.view.backgroundColor =  UIColor.UCAColor.AppThemeColor_1
        self.userHeaderName.textColor = UIColor.UCAColor.AppThemeTxtColor_1
        
        self.tableView.register(UINib(nibName: "MenuListTVCell", bundle: nil), forCellReuseIdentifier: "MenuListTVCell")
        menuUserAreaBgImgView.image = UIImage(named: "ic_menu_userarea_bg")
        
        self.tableView.bounces = false
        
        setUserInfo()
        setData()
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            GeneralFunctions.setImgTintColor(imgView: menuUserAreaBgImgView, color: UIColor.UCAColor.AppThemeColor)
        }
        
        self.navigationDrawerController?.delegate = self
        
        GeneralFunctions.setImgTintColor(imgView: settingsImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        let settingsTapGue = UITapGestureRecognizer()
        settingsTapGue.addTarget(self, action: #selector(self.settingIcTapped))
        settingsImgView.isUserInteractionEnabled = true
        settingsImgView.addGestureRecognizer(settingsTapGue)
    }
    
    override func viewDidLayoutSubviews() {
        if(isBottomViewSet == false){
            var topMargin = self.userDataInsideTopMargin.constant + GeneralFunctions.getSafeAreaInsets().top
            var topViewHeight = self.userDataContainerViewHeight.constant + GeneralFunctions.getSafeAreaInsets().top
            if(Configurations.isIponeXDevice()){
                topMargin = topMargin - self.userDataInsideTopMargin.constant
                topViewHeight = topViewHeight - self.userDataInsideTopMargin.constant
            }
            self.userDataInsideTopMargin.constant = topMargin
            self.userDataContainerViewHeight.constant = topViewHeight
            
            self.logOutViewHeight.constant = self.logOutViewHeight.constant + GeneralFunctions.getSafeAreaInsets().bottom
            
            if(Configurations.isIponeXDevice()){
                self.logOutViewHeight.constant = self.logOutViewHeight.constant - 20
            }
            isBottomViewSet = true
        }
    }

    
    func settingIcTapped(){
        
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.closeRightView()
        }else{
            self.navigationDrawerController?.closeLeftView()
        }
        
        let FEMALE_RIDE_REQ_ENABLE = userProfileJson.get("FEMALE_RIDE_REQ_ENABLE")
        
        
        if(FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES" || userProfileJson.get("eGender") == "Male"){
            openManageProfile(isOpenEditProfile: false)
        }else{
            if(self.userProfileJson.get("eGender") == "" && FEMALE_RIDE_REQ_ENABLE.uppercased() == "YES"){
                openGenderView()
            }else{
                let setPreferencesUV = GeneralFunctions.instantiateViewController(pageName: "SetPreferencesUV") as! SetPreferencesUV
                (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(setPreferencesUV, animated: true)
            }
            
        }
        
    }
    
    func openGenderView(){
        
        genderView = self.generalFunc.loadView(nibName: "GenderView", uv: self, isWithOutSize: true)
        
        genderView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: Application.screenSize.height)
        
        Application.window!.addSubview(genderView)
        
        let closeTapGue = UITapGestureRecognizer()
        closeTapGue.addTarget(self, action: #selector(self.closeGenderView))
        
        self.genderVCloseImgView.isUserInteractionEnabled = true
        self.genderVCloseImgView.addGestureRecognizer(closeTapGue)
        
        self.genderHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Select your gender to continue", key: "LBL_SELECT_GENDER")
        self.maleLbl.text = self.generalFunc.getLanguageLabel(origValue: "Male", key: "LBL_MALE_TXT")
        self.femaleLbl.text = self.generalFunc.getLanguageLabel(origValue: "Female", key: "LBL_FEMALE_TXT")
        GeneralFunctions.setImgTintColor(imgView: self.genderVCloseImgView, color: UIColor.white)
        
        let maleTapGue = UITapGestureRecognizer()
        maleTapGue.addTarget(self, action: #selector(self.maleImgTapped))
        
        self.maleImgView.isUserInteractionEnabled = true
        self.maleImgView.addGestureRecognizer(maleTapGue)
        
        
        let femaleTapGue = UITapGestureRecognizer()
        femaleTapGue.addTarget(self, action: #selector(self.femaleImgTapped))
        
        self.femaleImgView.isUserInteractionEnabled = true
        self.femaleImgView.addGestureRecognizer(femaleTapGue)
    }
    
    func maleImgTapped(){
        
        self.closeGenderView()
        updateUserGender(eGender: "Male")
    }
    
    func femaleImgTapped(){
        self.closeGenderView()
        updateUserGender(eGender: "Female")
    }
    
    func closeGenderView(){
        if(self.genderView != nil){
            self.genderView.removeFromSuperview()
        }
    }
    
    func updateUserGender(eGender:String){
        
        let parameters = ["type":"updateUserGender", "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "eGender": eGender]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
                    
                    self.userProfileJson = userProfileJson
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    func setUserInfo(){
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        self.userHeaderName.text = userProfileJson.get("vName").uppercased() + " " + userProfileJson.get("vLastName").uppercased()
        //        self.userHeaderName.fitText()
        self.walletHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Wallet Balance", key: "LBL_WALLET_BALANCE") + ":"
        self.walletVLbl.text = Configurations.convertNumToAppLocal(numStr: userProfileJson.get("user_available_balance"))
        
        usrProfileImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        self.walletHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        self.walletVLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        self.userHeaderName.textColor = UIColor.UCAColor.AppThemeTxtColor
        self.tableView.backgroundColor = UIColor.UCAColor.menuListBg
        
        self.walletAmountUpdateActIndicator.color = UIColor.UCAColor.AppThemeTxtColor
        
        if(Configurations.isRTLMode()){
            self.walletHLbl.textAlignment = .left
        }else{
            self.walletHLbl.textAlignment = .right
        }
    }
    
    func navigationDrawerController(navigationDrawerController: NavigationDrawerController, willOpen position: NavigationDrawerPosition) {
        setUserInfo()
        setData()
        let IS_WALLET_AMOUNT_UPDATE_KEY = GeneralFunctions.getValue(key: Utils.IS_WALLET_AMOUNT_UPDATE_KEY)
        
        if(IS_WALLET_AMOUNT_UPDATE_KEY != nil && (IS_WALLET_AMOUNT_UPDATE_KEY as! String) == "true" && userProfileJson.get("WALLET_ENABLE").uppercased() == "YES"){
            updateWalletAmount()
        }
    }
    
    
    
    func setData(){
        
        items.removeAll()
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MY_PROFILE_HEADER_TXT"),"Image" : "ic_Lmenu_profile","ID" : MENU_PROFILE] as NSDictionary)
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "My Availability", key: "LBL_MY_AVAILABILITY"),"Image" : "ic_Lmenu_my_avail","ID" : MENU_MY_AVAIL] as NSDictionary)
            
            
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES"),"Image" : "ic_Lmenu_manage_services","ID" : MENU_MANAGE_VEHICLES] as NSDictionary)
        }else{
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES"),"Image" : "ic_Lmenu_manage_vehicles","ID" : MENU_MANAGE_VEHICLES] as NSDictionary)
        }
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Manage Documents", key: "LBL_MANAGE_DOCUMENT"),"Image" : "ic_Lmenu_manage_doc","ID" : MENU_YOUR_DOC] as NSDictionary)
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Your Trips", key: "LBL_YOUR_TRIPS"),"Image" : "ic_Lmenu_booking_history","ID" : MENU_RIDE_HISTORY] as NSDictionary)
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Bank Details", key: "LBL_BANK_DETAILS_TXT"),"Image" : "ic_Lmenu_bank_Details","ID" : MENU_BANK_DETAILS] as NSDictionary)
        
        let APP_PAYMENT_MODE = userProfileJson.get("APP_PAYMENT_MODE")
        let WALLET_ENABLE = userProfileJson.get("WALLET_ENABLE")
        _ = userProfileJson.get("RIIDE_LATER")
        let REFERRAL_SCHEME_ENABLE = userProfileJson.get("REFERRAL_SCHEME_ENABLE")
        
        if(APP_PAYMENT_MODE.uppercased() != "CASH"){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PAYMENT"),"Image" : "ic_Lmenu_payment","ID" : MENU_PAYMENT] as NSDictionary)
        }
        
        if(WALLET_ENABLE.uppercased() == "YES"){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LEFT_MENU_WALLET"),"Image" : "ic_Lmenu_wallet","ID" : MENU_WALLET] as NSDictionary)
        }
        
        
        
        
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" || userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_TXT"),"Image" : "ic_Lmenu_privacy","ID" : MENU_ACCOUNT_VERIFY] as NSDictionary)
        }
        
        if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX ){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "My HeatView", key: "LBL_MENU_MY_HEATVIEW"),"Image" : "ic_Lmenu_heat_view","ID" : MENU_HEAT_VIEW] as NSDictionary)
        }
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMERGENCY_CONTACT"),"Image" : "ic_Lmenu_emergency","ID" : MENU_EMERGENCY] as NSDictionary)
        
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Rider Feedback", key: "LBL_RIDER_FEEDBACK"),"Image" : "ic_Lmenu_feedback","ID" : MENU_DRIVER_FEEDBACK] as NSDictionary)
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_STATISTICS_TXT"),"Image" : "ic_Lmenu_statistics","ID" : MENU_TRIP_STATISTICS] as NSDictionary)
        //
        
        //        if(RIIDE_LATER.uppercased() == "YES"){
        //            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MY_BOOKINGS"),"Image" : "ic_Lmenu_booking","ID" : MENU_BOOKINGS] as NSDictionary)
        //        }
        if(REFERRAL_SCHEME_ENABLE.uppercased() == "YES"){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INVITE_FRIEND_TXT"),"Image" : "ic_Lmenu_invite","ID" : MENU_INVITE_FRIENDS] as NSDictionary)
        }
        
        //        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ABOUT_US_TXT"),"Image" : "ic_Lmenu_aboutUs","ID" : MENU_ABOUTUS] as NSDictionary)
        //        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PRIVACY_POLICY_TEXT"),"Image" : "ic_Lmenu_privacy","ID" : MENU_PRIVACY] as NSDictionary)
        //        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT"),"Image" : "ic_Lmenu_contactUs","ID" : MENU_CONTACTUS] as NSDictionary)
        //        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HELP_TXT"),"Image" : "ic_Lmenu_help","ID" : MENU_HELP] as NSDictionary)
        //        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SIGNOUT_TXT"),"Image" : "ic_Lmenu_logOut","ID" : MENU_SIGN_OUT] as NSDictionary)
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SUPPORT_HEADER_TXT"),"Image" : "ic_Lmenu_support","ID" : MENU_SUPPORT] as NSDictionary)
        
        self.logOutLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SIGNOUT_TXT")
        self.logOutLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        self.logOutLbl.removeGestureRecognizer(self.logOutLbl.tapGue)
        GeneralFunctions.setImgTintColor(imgView: self.logOutImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        let signOutTapGue = UITapGestureRecognizer()
        self.logOutView.isUserInteractionEnabled = true
        signOutTapGue.addTarget(self, action: #selector(self.signOutTapped))
        self.logOutView.addGestureRecognizer(signOutTapGue)
        self.logOutView.backgroundColor = UIColor.UCAColor.AppThemeColor
        DispatchQueue.main.async() {
            self.tableView.allowsSelection = true
            self.tableView.delegate = self
            self.tableView.dataSource = self
            self.tableView.reloadData()
        }
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func signOutTapped(){
        
        closeDrawerView()
        
        self.generalFunc.setAlertMessage(uv: (self.navigationDrawerController?.rootViewController as! UINavigationController), title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LOGOUT"), content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WANT_LOGOUT_APP_TXT"), positiveBtn:self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YES"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO"), completionHandler: {(btnClickedId) in
            if(btnClickedId == 0)
            {
//                let window = UIApplication.shared.delegate!.window!
//                
//                let parameters = ["type":"updateDriverStatus", "latitude": "", "longitude": "", "Status": "Not Available", "GoogleServerKey": CommonUtils.google_server_key, "iDriverId": GeneralFunctions.getMemberd()]
//                
//                let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
//                exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
//                exeWebServerUrl.currInstance = exeWebServerUrl
//                exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
//                    
//                    if(response != ""){
//                        
//                        GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
//                        GeneralFunctions.logOutUser()
//                        GeneralFunctions.restartApp(window: window!)
//                        
//                    }else{
//                        self.generalFunc.setError(uv: self)
//                    }
//                })
                
                let window = UIApplication.shared.delegate!.window!
                
                let parameters = ["type":"callOnLogout", "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
                
                let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: (self.navigationDrawerController?.rootViewController as! UINavigationController).view, isOpenLoader: true)
                exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
                exeWebServerUrl.currInstance = exeWebServerUrl
                exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
                    
                    if(response != ""){
                        
                        GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                        
                        GeneralFunctions.logOutUser()
                        GeneralFunctions.restartApp(window: window!)
                        
                    }else{
                        self.generalFunc.setError(uv: (self.navigationDrawerController?.rootViewController as! UINavigationController))
                    }
                })
                
            }
            if(btnClickedId == 1)
            {
            }
        })
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return items.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let cell = tableView.dequeueReusableCell(withIdentifier: "MenuListTVCell", for: indexPath) as! MenuListTVCell
        cell.backgroundColor = UIColor.clear
        
        let title = items[indexPath.row].object(forKey: "Title") as! String
        let imageName = items[indexPath.row].object(forKey: "Image") as! String
        cell.menuTxtLbl.text = title
        cell.menuTxtLbl.removeGestureRecognizer(cell.menuTxtLbl.tapGue)
        cell.menuImgView.image = UIImage(named: imageName)
        cell.menuTxtLbl.textColor = UIColor.UCAColor.menuListTxtColor
        GeneralFunctions.setImgTintColor(imgView: cell.menuImgView, color: UIColor.UCAColor.menuListTxtColor)
        
        cell.selectionStyle = UITableViewCellSelectionStyle.none
        
        return cell
    }
    
    func closeDrawerView(){
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.closeRightView()
        }else{
            self.navigationDrawerController?.closeLeftView()
        }
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let selectedMenuId = items[indexPath.item].object(forKey: "ID") as! String
        
        closeDrawerView()
        
        let window = UIApplication.shared.delegate!.window!
        
        switch selectedMenuId {
        case MENU_PROFILE:
            openManageProfile(isOpenEditProfile: false)
            break
        case MENU_BANK_DETAILS:
            openBankDetails()
            break
        
        case MENU_PAYMENT:
            openPayment()
            break
        case MENU_WALLET:
            self.openMyWallet()
            break
        case MENU_RIDE_HISTORY:
            openHistory()
            break
        case MENU_ACCOUNT_VERIFY:
            openAccountVerify()
            break
        case MENU_HEAT_VIEW:
            openHeatView()
            break
        case MENU_MANAGE_VEHICLES:
            openManageVehicles()
            break
        case MENU_YOUR_DOC:
            openYourDocument()
            break
        
        case MENU_TRIP_STATISTICS:
            openTripStatistics()
            break
        case MENU_DRIVER_FEEDBACK:
            openFeedback()
            break
        case MENU_BOOKINGS:
            openMyBookings()
            break
        case MENU_INVITE_FRIENDS:
            openInviteFriends()
            break
        case MENU_ABOUTUS:
            openAbout()
            break
        case MENU_PRIVACY:
            openPrivacy()
            break
        case MENU_CONTACTUS:
            openContactUs()
            break
        case MENU_HELP:
            openHelp()
            break
        case MENU_SUPPORT:
            openSupport()
            break
        case MENU_EMERGENCY:
            openEmeContact()
            break
        case MENU_SIGN_OUT:
            self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LOGOUT"), content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WANT_LOGOUT_APP_TXT"), positiveBtn:self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YES"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO"), completionHandler: {(btnClickedId) in
                
                if(btnClickedId == 0)
                {
                    
                    GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                    
                    GeneralFunctions.logOutUser()
                    GeneralFunctions.restartApp(window: window!)
                    
                }
                if(btnClickedId == 1)
                {
                    
                }
            })
            break
        default:
            break
        }
        
    }
    
   
   
    func openManageProfile(isOpenEditProfile:Bool){
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.closeRightView()
        }else{
            self.navigationDrawerController?.closeLeftView()
        }
        
        let manageProfileUv = GeneralFunctions.instantiateViewController(pageName: "ManageProfileUV") as! ManageProfileUV
        manageProfileUv.isOpenEditProfile = isOpenEditProfile
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageProfileUv, animated: true)
    }
    
    func openBankDetails(){
        let bankDetailsUV = GeneralFunctions.instantiateViewController(pageName: "BankDetailsUV") as! BankDetailsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(bankDetailsUV, animated: true)
    }
    func openPayment(){
        
        let paymentUV = GeneralFunctions.instantiateViewController(pageName: "PaymentUV") as! PaymentUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(paymentUV, animated: true)
    }
    
    func openAccountVerify(){
        
        let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" && userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            accountVerificationUv.requestType = "DO_EMAIL_PHONE_VERIFY"
        }else if(userProfileJson.get("eEmailVerified").uppercased() != "YES"){
            accountVerificationUv.requestType = "DO_EMAIL_VERIFY"
        }else{
            accountVerificationUv.requestType = "DO_PHONE_VERIFY"
        }
        accountVerificationUv.menuScreenUv = self
        self.pushToNavController(uv: accountVerificationUv)
    }
    
    func openHeatView(){
        
        let heatViewUV = GeneralFunctions.instantiateViewController(pageName: "HeatViewUV") as! HeatViewUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(heatViewUV, animated: true)
    }
    
    func openMyWallet(){
        
        let manageWalletUV = GeneralFunctions.instantiateViewController(pageName: "ManageWalletUV") as! ManageWalletUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageWalletUV, animated: true)
    }
    
    func openYourDocument(){
        
        let listOfDocumentUV = GeneralFunctions.instantiateViewController(pageName: "ListOfDocumentUV") as! ListOfDocumentUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(listOfDocumentUV, animated: true)
    }
    
    func openManageVehicles(){
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
//            if(self.userProfileJson.get("UBERX_PARENT_CAT_ID") == "0"){
                let manageServicesUV = GeneralFunctions.instantiateViewController(pageName: "ManageServicesUV") as! ManageServicesUV
                manageServicesUV.iVehicleCategoryId = self.userProfileJson.get("UBERX_PARENT_CAT_ID")
                (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageServicesUV, animated: true)
//            }else{
//                let updateServiceUv = GeneralFunctions.instantiateViewController(pageName: "UpdateServicesUV") as! UpdateServicesUV
//                updateServiceUv.vTitle = ""
//                updateServiceUv.iVehicleCategoryId = self.userProfileJson.get("UBERX_PARENT_CAT_ID")
//
//                (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(updateServiceUv, animated: true)
//            }
            
        }else{
            let manageVehiclesUV = GeneralFunctions.instantiateViewController(pageName: "ManageVehiclesUV") as! ManageVehiclesUV
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageVehiclesUV, animated: true)
        }
    }
    
    func openTripStatistics(){
        
        let statisticsUV = GeneralFunctions.instantiateViewController(pageName: "StatisticsUV") as! StatisticsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(statisticsUV, animated: true)
    }
    
    func openHistory(){
               
        let rideHistoryUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        let myBookingsUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        let pendingBookingsUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        rideHistoryUv.HISTORY_TYPE = "PAST"
        myBookingsUv.HISTORY_TYPE = "LATER"
        pendingBookingsUv.HISTORY_TYPE = "PENDING"
        
        rideHistoryUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "PAST", key: "LBL_PAST").uppercased()
        myBookingsUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "UPCOMING", key: "LBL_UPCOMING").uppercased()
        pendingBookingsUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "UPCOMING", key: "LBL_PENDING").uppercased()
        
        var uvArr = [UIViewController]()
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            uvArr += [pendingBookingsUv]
            uvArr += [myBookingsUv]
            uvArr += [rideHistoryUv]
        }else{
            uvArr += [rideHistoryUv]
            uvArr += [myBookingsUv]
        }
        
        if(self.userProfileJson.get("RIDE_LATER_BOOKING_ENABLED").uppercased() == "YES"){
            let rideHistoryTabUv = RideHistoryTabUV(viewControllers: uvArr, selectedIndex: 0)
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(rideHistoryTabUv, animated: true)
        }else{
            rideHistoryUv.isDirectPush = true
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(rideHistoryUv, animated: true)
        }
        
        
        //        (self.navigationDrawerController?.rootViewController)?.present(rideHistoryTabUv, animated: true, completion: nil)
        
    }
    
    func openFeedback(){
        
        let feedbackUV = GeneralFunctions.instantiateViewController(pageName: "DriverFeedBackUV") as! DriverFeedBackUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(feedbackUV, animated: true)
        
    }
    
    func openMyBookings(){
    }
    
    func openInviteFriends(){
        
        let inviteFriendsUv = GeneralFunctions.instantiateViewController(pageName: "InviteFriendsUV") as! InviteFriendsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(inviteFriendsUv, animated: true)
    }
    
    func openAbout(){
        
        let staticPageUV = GeneralFunctions.instantiateViewController(pageName: "StaticPageUV") as! StaticPageUV
        staticPageUV.STATIC_PAGE_ID = "1"
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(staticPageUV, animated: true)
    }
    
    func openPrivacy(){
        
        let staticPageUV = GeneralFunctions.instantiateViewController(pageName: "StaticPageUV") as! StaticPageUV
        staticPageUV.STATIC_PAGE_ID = "33"
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(staticPageUV, animated: true)
    }
    
    func openContactUs(){
        
        let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(contactUsUv, animated: true)
    }
    
    func openSupport(){
        
        let supportUv = GeneralFunctions.instantiateViewController(pageName: "SupportUV") as! SupportUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(supportUv, animated: true)
    }
    
    func openHelp(){
        
        let helpUv = GeneralFunctions.instantiateViewController(pageName: "HelpUV") as! HelpUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(helpUv, animated: true)
    }
    
    func openEmeContact(){
        
        let emergencyContactsUv = GeneralFunctions.instantiateViewController(pageName: "EmergencyContactsUV") as! EmergencyContactsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(emergencyContactsUv, animated: true)
    }
    
    func updateWalletAmount(){
        walletAmountUpdateActIndicator.startAnimating()
        walletAmountUpdateActIndicatorContainerView.isHidden = false
        self.walletVLbl.text = ""
        let parameters = ["type":"GetMemberWalletBalance", "iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            self.walletAmountUpdateActIndicator.stopAnimating()
            self.walletAmountUpdateActIndicatorContainerView.isHidden = true
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                Utils.printLog(msgData: "dataDict:Balance:\(dataDict)")
                if(dataDict.get("Action") == "1"){
                    self.walletVLbl.text = Configurations.convertNumToAppLocal(numStr: dataDict.get("MemberBalance"))
                    GeneralFunctions.removeValue(key: Utils.IS_WALLET_AMOUNT_UPDATE_KEY)
                }else{
                    self.walletVLbl.text = "--"
                }
                
            }else{
                self.walletVLbl.text = "--"
            }
        })
    }
}

