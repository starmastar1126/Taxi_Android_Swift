//
//  MenuScreenUV.swift
//  PassengerApp
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
    var MENU_ACCOUNT_VERIFY = "13"
    var MENU_ON_GOING_TRIPS = "14"
    
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
        
//        let HANDICAP_ACCESSIBILITY_OPTION = userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION")
//        let FEMALE_RIDE_REQ_ENABLE = userProfileJson.get("FEMALE_RIDE_REQ_ENABLE")
//        
//        if(HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES" || FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES"){
        openManageProfile(isOpenEditProfile: false)
//        }else{
//            let setPreferencesUV = GeneralFunctions.instantiateViewController(pageName: "SetPreferencesUV") as! SetPreferencesUV
//            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(setPreferencesUV, animated: true)
//        }
        
    }
    
    func setUserInfo(){
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        self.userHeaderName.text = userProfileJson.get("vName") + " " + userProfileJson.get("vLastName")
//        self.userHeaderName.fitText()
        self.walletHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Wallet Balance", key: "LBL_WALLET_BALANCE") + ":"
//        self.walletVLbl.text = userProfileJson.get("user_available_balance")
        self.walletVLbl.text = Configurations.convertNumToAppLocal(numStr: userProfileJson.get("user_available_balance"))
        
        usrProfileImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImgName")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
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
        self.items.removeAll()
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MY_PROFILE_HEADER_TXT"),"Image" : "ic_Lmenu_profile","ID" : MENU_PROFILE] as NSDictionary)
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Your trips", key: "LBL_YOUR_TRIPS"),"Image" : "ic_Lmenu_booking_history","ID" : MENU_RIDE_HISTORY] as NSDictionary)

        
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
        
        if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LEFT_MENU_ONGOING_TRIPS"),"Image" : "ic_menu_trip","ID" : MENU_ON_GOING_TRIPS] as NSDictionary)
        }
        
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" || userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_TXT"),"Image" : "ic_Lmenu_privacy","ID" : MENU_ACCOUNT_VERIFY] as NSDictionary)
        }

        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EMERGENCY_CONTACT"),"Image" : "ic_Lmenu_emergency","ID" : MENU_EMERGENCY] as NSDictionary)

        
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
        case MENU_PAYMENT:
            openPayment()
            break
        case MENU_WALLET:
            self.openMyWallet()
            break
        case MENU_RIDE_HISTORY:
            openHistory()
            break
        case MENU_ON_GOING_TRIPS:
            openMyOnGoingTrips()
            break
        case MENU_ACCOUNT_VERIFY:
            openAccountVerify()
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
    
    func openPayment(){
        let paymentUV = GeneralFunctions.instantiateViewController(pageName: "PaymentUV") as! PaymentUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(paymentUV, animated: true)
    }
    
    func openMyWallet(){
        let manageWalletUV = GeneralFunctions.instantiateViewController(pageName: "ManageWalletUV") as! ManageWalletUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageWalletUV, animated: true)
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
    
    func openMyOnGoingTrips(){
        let myOnGoingTripsUV = GeneralFunctions.instantiateViewController(pageName: "MyOnGoingTripsUV") as! MyOnGoingTripsUV
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(myOnGoingTripsUV, animated: true)
    }
    
    func openHistory(){
        
        let rideHistoryUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        let myBookingsUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        rideHistoryUv.HISTORY_TYPE = "PAST"
        rideHistoryUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "PAST", key: "LBL_PAST").uppercased()
        
        myBookingsUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "UPCOMING", key: "LBL_UPCOMING").uppercased()
        myBookingsUv.HISTORY_TYPE = "LATER"
        
        if(self.userProfileJson.get("RIDE_LATER_BOOKING_ENABLED").uppercased() == "YES"){
            let rideHistoryTabUv = RideHistoryTabUV(viewControllers: [rideHistoryUv, myBookingsUv], selectedIndex: 0)
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(rideHistoryTabUv, animated: true)
        }else{
            rideHistoryUv.isDirectPush = true
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(rideHistoryUv, animated: true)
        }
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

