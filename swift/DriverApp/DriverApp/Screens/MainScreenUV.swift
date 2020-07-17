//
//  MainScreenUV.swift
//  DriverApp
//
//  Created by NEW MAC on 11/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation
import GoogleMaps

class MainScreenUV: UIViewController, OnLocationUpdateDelegate, CMSwitchViewDelegate, OnTaskRunCalledDelegate, GMSMapViewDelegate, MyLabelClickDelegate, UITableViewDelegate, UITableViewDataSource, AddressFoundDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
    @IBOutlet weak var statusLbl: MyLabel!
    @IBOutlet weak var statusSwitch: CMSwitchView!
    @IBOutlet weak var changeCarLbl: MyLabel!
    @IBOutlet weak var carNameLBl: MyLabel!
    @IBOutlet weak var carNumLbl: MyLabel!
    @IBOutlet weak var userPicImgView: UIImageView!
    @IBOutlet weak var myLocImgView: UIImageView!
    @IBOutlet weak var heatMapImgView: UIImageView!
    
    @IBOutlet weak var ufxScrollView: UIScrollView!
    @IBOutlet weak var driverDetailBottomCOntainerViewHeight: NSLayoutConstraint!
    
    // UFXMain screen OutLets
    @IBOutlet weak var ufxHeaderView: UIView!
    @IBOutlet weak var jobLocHLbl: MyLabel!
    @IBOutlet weak var jobLocAddLbl: MyLabel!
    @IBOutlet weak var availabilityRadiusLbl: MyLabel!
    @IBOutlet weak var availRadiusLblWidth: NSLayoutConstraint!
    @IBOutlet weak var editAvailRadiusImgView: UIImageView!
    @IBOutlet weak var upcomingJobCountLbl: MyLabel!
    @IBOutlet weak var jobAreaSeperatorView: UIView!
    @IBOutlet weak var pendingJobAreaView: UIView!
    @IBOutlet weak var upcomingJobAreaView: UIView!
    @IBOutlet weak var pendingJobCountLbl: MyLabel!
    @IBOutlet weak var pendingJobLbl: MyLabel!
    @IBOutlet weak var upcomingJobLbl: MyLabel!
    @IBOutlet weak var ufxUserNameLbl: MyLabel!
    @IBOutlet weak var ufxJobLocAreaHeight: NSLayoutConstraint!
    
    // Select Car Design Outlets
    @IBOutlet weak var selectCarHImgView: UIImageView!
    @IBOutlet weak var selectCarHLbl: MyLabel!
    @IBOutlet weak var selectCarTableView: UITableView!
    @IBOutlet weak var addNewVehicleLbl: MyLabel!
    @IBOutlet weak var manageVehiclesLbl: MyLabel!
//    var HailEnableOnDriverStatus = ""
    
    var userProfileJson:NSDictionary!
    
    var navItem:UINavigationItem!
    
    
    var getAddressFromLocation:GetAddressFromLocation!
    
    //    var menuScreenUv:MenuScreenUV!
    
    var gMapView:GMSMapView!
    
    let generalFunc = GeneralFunctions()
    
    var getLocation:GetLocation!
    
    var isDriverOnline:Bool = false
    
    var currentLocation:CLLocation?
    
    var isHeatMapEnabled = false
    
    var task_update_heatMapData: ExeServerUrl?
    
    var configPubNub:ConfigPubNub?
    
    var historyData = [String]()
    var onlineData = [String]()
    
    var currentRadius = 0.0
    var dtaCircleHeatMap = [GMSMarker]()
    
//    var zoomLevel:Float  = 4
    
    var updateCurrentReqFreqTask:UpdateFreqTask!
    var currentReqTaskPosition = 0
    var isFirstLocationUpdate = true
    
    var window:UIWindow!
    
    var isDataSet = false
    
    var updateDriverStatus:UpdateDriverStatus!
    
    var carListDataArrList = [NSDictionary]()
    
    var selectCarView:UIView!
    var selectCarBGView:UIView!
    
    let userLocTapGue = UITapGestureRecognizer()
    
    var locationDialog:OpenLocationEnableView!
    
    var isMyLocationEnabled = true
    
    private var gradientColors = [UIColor.red , UIColor.white]
    private var gradientStartPoints = [0.005,1.0]
    private var heatmapLayer: GMUHeatmapTileLayer!
    private var onlineHeatmapLayer: GMUHeatmapTileLayer!
    private var onlineGradientColors = [UIColor.green]
    private var onlineGradientStartPoints = [0.2]
    
    var isCameraUpdateIgnore = false
    
    var ufxPAGE_HEIGHT:CGFloat = 550
    var ufxCntView:UIView!
    var cntView:UIView!
    
    var isSafeAreaSet = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        if(self.userProfileJson != nil){
            let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
            self.userProfileJson = userProfileJson
            userPicImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
            
            
            if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
                self.navigationController?.navigationBar.layer.zPosition = -1
                
                if(self.ufxUserNameLbl != nil){
                    self.ufxUserNameLbl.text = userProfileJson.get("vName").uppercased() + " " + userProfileJson.get("vLastName").uppercased()
                    
                    getProviderStates()
                }
            }
        }
    }
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
                driverDetailBottomCOntainerViewHeight.constant = driverDetailBottomCOntainerViewHeight.constant + GeneralFunctions.getSafeAreaInsets().bottom
                if(Configurations.isIponeXDevice()){
                    self.driverDetailBottomCOntainerViewHeight.constant = self.driverDetailBottomCOntainerViewHeight.constant - 20
                }
            }
            
            if(ufxCntView != nil){
                ufxCntView.frame.size.height = ufxCntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
            cntView = self.generalFunc.loadView(nibName: "MainScreenDesign", uv: self, contentView: contentView)
            
//            cntView.frame.size = CGSize(width: Application.screenSize.width, height: Application.screenSize.height)
            self.contentView.addSubview(cntView)
        }else{
//            self.contentView.addSubview(self.generalFunc.loadView(nibName: "UFXMainScreenDesign", uv: self, contentView: contentView))
            
            ufxCntView = self.generalFunc.loadView(nibName: "UFXMainScreenDesign", uv: self, contentView: self.ufxScrollView)
            ufxCntView.frame.size = CGSize(width: ufxCntView.frame.width, height: ufxPAGE_HEIGHT)
            
            
            self.ufxHeaderView.backgroundColor = UIColor.UCAColor.AppThemeColor
            
            if(self.userProfileJson.get("RIDE_LATER_BOOKING_ENABLED").uppercased() != "YES" && self.pendingJobAreaView != nil && self.jobAreaSeperatorView != nil){
                self.pendingJobAreaView.isHidden = true
                self.jobAreaSeperatorView.isHidden = true
                self.upcomingJobAreaView.isHidden = true
                ufxPAGE_HEIGHT = ufxPAGE_HEIGHT - 150
            }
            
            self.ufxScrollView.contentSize = CGSize(width: self.ufxScrollView.frame.size.width, height: ufxPAGE_HEIGHT)
            self.ufxScrollView.isHidden = false
            self.ufxScrollView.addSubview(ufxCntView)
            self.ufxScrollView.bounces = false
        }
        
        Utils.driverMarkersPositionList.removeAll()
        Utils.driverMarkerAnimFinished = true
        
        window = Application.window!
        
        Utils.createRoundedView(view: userPicImgView, borderColor: Color.clear, borderWidth: 0)
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX ){
            heatMapImgView.isHidden = true
        }
      
        
       
        
        GeneralFunctions.removeValue(key: "OPEN_MSG_SCREEN")
        
        setData()
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
    }
    
    deinit {
        releaseAllTask()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if(self.userProfileJson != nil){
           
            if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
                self.navigationController?.navigationBar.layer.zPosition = 1
            }
        }
        
    }
    
    
    override func didReceiveMemoryWarning() {
//        Utils.printLog(msgData: "MemoryWarningReceived")
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isDataSet == false){
            DispatchQueue.main.async {
                let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: 0.0)
                self.gMapView = GMSMapView.map(withFrame: self.googleMapContainerView.frame, camera: camera)
                //            gMapView = GMSMapView.map(withFrame: CGRect(x: 0, y:0, width: Application.screenSize.width, height: Application.screenSize.height), camera: camera)
                
                if(GeneralFunctions.hasLocationEnabled() == true){
                    self.gMapView.isMyLocationEnabled = self.isMyLocationEnabled
                }
                
                self.gMapView.delegate = self
                self.googleMapContainerView.addSubview(self.gMapView)
                
                if(self.ufxCntView != nil){
                    self.ufxCntView.frame.size = CGSize(width: self.ufxCntView.frame.width, height: self.ufxPAGE_HEIGHT)
                    self.ufxScrollView.contentSize = CGSize(width: self.ufxScrollView.frame.size.width, height: self.ufxPAGE_HEIGHT)
                }
                
                self.checkLocationEnabled()
                self.isDataSet = true
            }
        }
    }
    
    func setData(){
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        GeneralFunctions.saveValue(key: "IS_DRIVER_ONLINE", value: "false" as AnyObject)
        
        userPicImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        
        self.statusSwitch.delegate = self
        
        
            self.changeCarLbl.isHidden = false
            self.carNumLbl.isHidden = false
            
            self.changeCarLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHANGE").uppercased()
            self.changeCarLbl.setClickDelegate(clickDelegate: self)
            self.carNumLbl.text = userProfileJson.get("vLicencePlateNo") == "" ? "xx xx 0000" : userProfileJson.get("vLicencePlateNo")
            self.carNameLBl.text = (userProfileJson.get("vMake") == "" && userProfileJson.get("vModel") == "") ? "xxx xxx" : (userProfileJson.get("vMake") + " " + userProfileJson.get("vModel"))
	    self.carNumLbl.textColor = UIColor.white
            
            statusSwitch.dotColor = UIColor(hex: 0xFF0000)
            statusSwitch.color = UIColor(hex: 0xFFFFFF)
            statusSwitch.tintColor = UIColor(hex: 0xFFFFFF)
        
        
        self.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GO_ONLINE_TXT").uppercased()
        
        let heatViewTapGue = UITapGestureRecognizer()
        heatViewTapGue.addTarget(self, action: #selector(self.heatViewTapped(sender:)))
        heatMapImgView.isUserInteractionEnabled = true
        
        heatMapImgView.addGestureRecognizer(heatViewTapGue)
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX ){
            heatMapImgView.isHidden = true
            heatMapImgView.frame.origin.x = heatMapImgView.frame.origin.x - heatMapImgView.frame.size.width - 15
        }
        
        if(self.getPubNubConfig().uppercased() == "YES"){
            configPubNub = ConfigPubNub()
            configPubNub!.buildPubNub()
        }
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        self.userLocTapGue.addTarget(self, action: #selector(self.myLocImgTapped))
        self.myLocImgView.isUserInteractionEnabled = true
        self.myLocImgView.addGestureRecognizer(self.userLocTapGue)
        
        checkPendingRequests()
        
        deleteTripStatusMessages()
        
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" || userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            let verifyBtn = Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_TXT"), uv: self, btnTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_VERIFY_TXT"), delayShow: 1, delayHide: 15)
            verifyBtn.addTarget(self, action: #selector(self.openAccountVerify(sender:)), for: UIControlEvents.touchUpInside)
        }
        
        
        //        LocalNotification.dispatchlocalNotification(with: "Notification Title for iOS10+", body: "This is the notification body, works on all versions", at: Date().addedBy(seconds: 10))
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func setUFXRadiusLblFrame(){
        
        let totalWidth = self.availabilityRadiusLbl.text!.width(withConstrainedHeight: 20, font: UIFont(name: "Roboto-Light", size: 17)!) + 10
        let availWidth = Application.screenSize.width - 90
        
        if(totalWidth > availWidth){
            self.availRadiusLblWidth.constant = availWidth
        }else{
            self.availRadiusLblWidth.constant = totalWidth
        }
    
    }
    
    
    
    func getProviderStates(){
        
        let parameters = ["type":"GetUserStats", "iDriverId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.pendingJobCountLbl.text = dataDict.get("Pending_Count")
                    self.upcomingJobCountLbl.text = dataDict.get("Upcoming_Count")
                    
                    self.availabilityRadiusLbl.text = "\(self.generalFunc.getLanguageLabel(origValue: "Within", key: "LBL_WITHIN")) \(dataDict.get("Radius")) \(self.generalFunc.getLanguageLabel(origValue: "", key: self.userProfileJson.get("eUnit") == "KMs" ? "LBL_KM_DISTANCE_TXT" : "LBL_MILE_DISTANCE_TXT")) \(self.generalFunc.getLanguageLabel(origValue: "Work Radius", key: "LBL_RADIUS"))"
                    
                    self.setUFXRadiusLblFrame()
                }
                
            }else{
                
//                if(isAlertShown == true){
//                    self.generalFunc.setError(uv: self)
//                }
            }
            
        })
    }
    
    
    
    func checkLocationEnabled(){
        if(locationDialog != nil){
            locationDialog.removeView()
            locationDialog = nil
        }
        
        if(GeneralFunctions.hasLocationEnabled() == false || InternetConnection.isConnectedToNetwork() == false){
            
//            locationDialog = OpenLocationEnableView(uv: self, containerView: self.cntView, gMapView: self.gMapView, isMapLocEnabled: isMyLocationEnabled)
            locationDialog = OpenLocationEnableView(uv: self, containerView: self.cntView == nil ? (self.ufxCntView == nil ? UIView() : self.ufxCntView) : self.cntView , gMapView: self.gMapView, isMapLocEnabled: isMyLocationEnabled)
            locationDialog.show()
            
            if(GeneralFunctions.hasLocationEnabled() == false){
                goOffline(isAlertShown: false)
            }
            
            return
        }else{
            if(self.gMapView != nil && self.gMapView.isMyLocationEnabled != self.isMyLocationEnabled){
                self.gMapView.isMyLocationEnabled = self.isMyLocationEnabled
            }
        }
        
    }
    
    func appInForground(){
        checkLocationEnabled()
        
        //        if(self.configPubNub != nil && self.isDriverOnline == true){
        //            self.configPubNub!.subscribeToCabReqChannel()
        //        }
        
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
//        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        if(userProfileJson != nil && userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.navigationController?.navigationBar.layer.zPosition = -1
            
            if(self.ufxUserNameLbl != nil){                
                getProviderStates()
            }
        }
    }
    
    func openAccountVerify(sender:FlatButton){
        
        self.snackbarController?.animate(snackbar: .hidden, delay: 0)
        
        let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" && userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            accountVerificationUv.requestType = "DO_EMAIL_PHONE_VERIFY"
        }else if(userProfileJson.get("eEmailVerified").uppercased() != "YES"){
            accountVerificationUv.requestType = "DO_EMAIL_VERIFY"
        }else{
            accountVerificationUv.requestType = "DO_PHONE_VERIFY"
        }
        accountVerificationUv.mainScreenUv = self
        self.pushToNavController(uv: accountVerificationUv)
    }
    
    func myLocImgTapped(){
        if(GeneralFunctions.hasLocationEnabled() == true){
            if(self.currentLocation == nil){
                return
            }
//            self.gMapView.camera.zoom
            let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation!.coordinate.latitude,
                                                  longitude: self.currentLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
            
            self.gMapView.animate(to: camera)
        }
        else{
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GPSENABLE_TXT"))
        }
    }
    
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.changeCarLbl){
            self.loadAvailableCar()
        }else if(self.manageVehiclesLbl != nil && sender == self.manageVehiclesLbl){
            self.selectCarBGView.removeFromSuperview()
            self.selectCarView.removeFromSuperview()
            
            openManageVehiclesScreen()
        }else if(self.addNewVehicleLbl != nil && sender == self.addNewVehicleLbl){
            self.selectCarBGView.removeFromSuperview()
            self.selectCarView.removeFromSuperview()
            
            let addVehiclesUv = GeneralFunctions.instantiateViewController(pageName: "AddVehiclesUV") as! AddVehiclesUV
            addVehiclesUv.isFromMainPage = true
            addVehiclesUv.mainScreenUv = self
//            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(addVehiclesUv, animated: true)
             self.pushToNavController(uv: addVehiclesUv)
        }else if(self.pendingJobCountLbl != nil && sender == self.pendingJobCountLbl){
            openHistory(isFirstUpcoming: false)
        }else if(self.upcomingJobCountLbl != nil && sender == self.upcomingJobCountLbl){
            openHistory(isFirstUpcoming: true)
        }
    }
    
    
    func openHistory(isFirstUpcoming:Bool){
        
        let rideHistoryUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        let myBookingsUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        let pendingBookingsUv = GeneralFunctions.instantiateViewController(pageName: "RideHistoryUV") as! RideHistoryUV
        rideHistoryUv.HISTORY_TYPE = "PAST"
        myBookingsUv.HISTORY_TYPE = "LATER"
        pendingBookingsUv.HISTORY_TYPE = "PENDING"
        
        rideHistoryUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "PAST", key: "LBL_PAST")
        myBookingsUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "UPCOMING", key: "LBL_UPCOMING")
        pendingBookingsUv.pageTabBarItem.title = self.generalFunc.getLanguageLabel(origValue: "UPCOMING", key: "LBL_PENDING")
        
        var uvArr = [UIViewController]()
        
        if(self.userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            if(isFirstUpcoming == true){
                uvArr += [myBookingsUv]
                uvArr += [pendingBookingsUv]
                uvArr += [rideHistoryUv]
            }else{
                uvArr += [pendingBookingsUv]
                uvArr += [myBookingsUv]
                uvArr += [rideHistoryUv]
            }
        }else{
            uvArr += [rideHistoryUv]
            uvArr += [myBookingsUv]
        }
        if(self.userProfileJson.get("RIDE_LATER_BOOKING_ENABLED").uppercased() == "YES"){
            let rideHistoryTabUv = RideHistoryTabUV(viewControllers: uvArr, selectedIndex: 0)
            self.pushToNavController(uv: rideHistoryTabUv)
        }else{
            rideHistoryUv.isDirectPush = true
            self.pushToNavController(uv: rideHistoryUv)
        }
        
    }
    
    func openManageVehiclesScreen(){
        let manageVehiclesUV = GeneralFunctions.instantiateViewController(pageName: "ManageVehiclesUV") as! ManageVehiclesUV
//        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageVehiclesUV, animated: true)
        self.pushToNavController(uv: manageVehiclesUV)
    }
    
    func releaseAllTask(isDismiss:Bool = true){
        
        if(updateDriverStatus != nil){
            updateDriverStatus.stopFrequentUpdate()
        }
        
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        if(configPubNub != nil){
            configPubNub!.releasePubNub()
            configPubNub = nil
        }
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
        if(isDismiss){
            self.dismiss(animated: false, completion: nil)
            self.navigationController?.dismiss(animated: false, completion: nil)
        }
    }
    
    
    func onLocationUpdate(location: CLLocation) {
        
        if(gMapView == nil){
            releaseAllTask()
            return
        }
        
        self.currentLocation = location
        
        let vAvailability = userProfileJson.get("vAvailability")
        
        if(vAvailability == "Available" && isFirstLocationUpdate == true && userProfileJson.get("eEmailVerified").uppercased() == "YES" && userProfileJson.get("ePhoneVerified").uppercased() == "YES" ){
            
            //            self.setSwitchStatusAvoidUpdate(value: true, isAnim: false)
            updateStatus(offline: false, isAlertShown: true)
            //            self.setOnlineState()
        }
        
        let GO_ONLINE = GeneralFunctions.getValue(key: "GO_ONLINE")
        
        if(GO_ONLINE != nil && (GO_ONLINE as! String) == "1"){
            //            self.setSwitchStatus(value: true, isAnim: false)
            updateStatus(offline: false, isAlertShown: true)
            GeneralFunctions.removeValue(key: "GO_ONLINE")
        }
        
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                              longitude: location.coordinate.longitude, zoom: currentZoomLevel)
        
        //        self.gMapView.animate(to: camera)
        if isHeatMapEnabled == false{
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
        }
        
        
        if(isFirstLocationUpdate == true && userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.jobLocAddLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LOAD_ADDRESS")
            
            self.findUserLocAddress()
        }
        
        isFirstLocationUpdate = false
        
        updateLocationToPubNub()
        
    }
    
    func onRefreshCalled(){
        findUserLocAddress()
        getProviderStates()
    }
    
    func findUserLocAddress(){
        if(self.currentLocation == nil){
            return
        }
        getAddressFromLocation = GetAddressFromLocation(uv: self, addressFoundDelegate: self)
        getAddressFromLocation.setLocation(latitude: self.currentLocation!.coordinate.latitude, longitude: self.currentLocation!.coordinate.longitude)
        getAddressFromLocation.executeProcess(isOpenLoader: false, isAlertShow: false)
    }
    
    func onAddressFound(address: String, location: CLLocation, isPickUpMode: Bool, dataResult: String) {
        getAddressFromLocation.addressFoundDelegate = nil
        getAddressFromLocation = nil
        
        self.jobLocAddLbl.text = address
        self.jobLocAddLbl.fitText()
        
        let height = address.height(withConstrainedWidth: Application.screenSize.width - 20, font: self.jobLocAddLbl.font!)
//        self.ufxJobLocAreaHeight.constant = self.ufxJobLocAreaHeight.constant + height - 20
        self.ufxJobLocAreaHeight.constant = 105 + height - 20
        ufxPAGE_HEIGHT = ufxPAGE_HEIGHT  + height - 20
        ufxCntView.frame.size = CGSize(width: ufxCntView.frame.width, height: ufxPAGE_HEIGHT)
        self.ufxScrollView.contentSize = CGSize(width: self.ufxScrollView.frame.size.width, height: ufxPAGE_HEIGHT)
    }
    
    
    func checkPendingRequests(){
        GeneralFunctions.saveValue(key: Utils.DRIVER_CURRENT_REQ_OPEN_KEY, value: "false" as AnyObject)
        let currentReqArr = userProfileJson.getArrObj("CurrentRequests")
        
        if(currentReqArr.count > 0){
            updateCurrentReqFreqTask = UpdateFreqTask(interval: 5)
            updateCurrentReqFreqTask.currInst = updateCurrentReqFreqTask
            updateCurrentReqFreqTask.setTaskRunListener(onTaskRunCalled: self)
            updateCurrentReqFreqTask.startRepeatingTask()
        }else{
            for (key, value) in UserDefaults.standard.dictionaryRepresentation() {
                
                if key.hasPrefix(Utils.DRIVER_REQ_CODE_PREFIX_KEY) {
                    
                    let dataValue = Int64(value as! String)
                    let day = 1000 * 60 * 60 * 24 * 1
                    let currentTimeInmill = Utils.currentTimeMillis() - Int64(day)
                    
                    if(currentTimeInmill > dataValue!){
                        GeneralFunctions.removeValue(key: key)
                    }
                    
                }
                
            }
        }
    }
    
    func deleteTripStatusMessages(){
        for (key, value) in UserDefaults.standard.dictionaryRepresentation() {
            
            if key.hasPrefix(Utils.TRIP_STATUS_MSG_PRFIX) {
                
                let dataValue = Int64(value as! String)
                let day = 1000 * 60 * 60 * 24 * 1
                let currentTimeInmill = Utils.currentTimeMillis() - Int64(day)
                
                if(currentTimeInmill > dataValue!){
                    GeneralFunctions.removeValue(key: key)
                }
                
            }
            
        }
    }
    
    func onTaskRun(currInst: UpdateFreqTask) {
        if(GeneralFunctions.getValue(key: Utils.DRIVER_CURRENT_REQ_OPEN_KEY) != nil && (GeneralFunctions.getValue(key: Utils.DRIVER_CURRENT_REQ_OPEN_KEY) as! String == "true")){
            return
        }
        let currentReqArr = userProfileJson!.getArrObj("CurrentRequests")
        
        
        if(currentReqTaskPosition < currentReqArr.count){
            
            let msg_str = currentReqArr[currentReqTaskPosition] as! NSDictionary
            
            
            let message = msg_str.get("tMessage")
            
            let msgDict = message.getJsonDataDict()
            
            let msgCode = msgDict.get("MsgCode")
            
            let codeValue = GeneralFunctions.getValue(key: Utils.DRIVER_REQ_CODE_PREFIX_KEY + msgCode)
            if(codeValue == nil){
                
                NotificationCenter.default.post(name: NSNotification.Name(rawValue: Utils.passengerRequestArrived), object: self, userInfo: ["body":message])
            }
            
            currentReqTaskPosition = currentReqTaskPosition + 1
            
            return
        }else{
            updateCurrentReqFreqTask.stopRepeatingTask()
        }
    }
    
    
    func heatViewTapped(sender:UITapGestureRecognizer){
        
        gMapView.clear()
        
        onlineData.removeAll()
        historyData.removeAll()
        dtaCircleHeatMap.removeAll()
        currentRadius = 0
        
        if(isHeatMapEnabled == false){
            isHeatMapEnabled = true
            gMapView.delegate = self
            heatMapImgView.image = UIImage(named: "ic_heat_map_on")
            
            
//            zoomLevel = self.gMapView.camera.zoom
            
            let camera = GMSCameraPosition.camera(withLatitude: getCenterCoordinate().latitude, longitude: getCenterCoordinate().longitude, zoom: 14)
            
            self.isCameraUpdateIgnore = true
            
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            
            loadHeatMapData()
            
        }else{
            isHeatMapEnabled = false
            gMapView.delegate = nil
            heatMapImgView.image = UIImage(named: "ic_heat_map_off")
            
            if(self.currentLocation != nil){
                let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation!.coordinate.latitude, longitude: self.currentLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                
                self.gMapView.animate(with: GMSCameraUpdate.setCamera(camera))
            }else{
                
                let camera = GMSCameraPosition.camera(withLatitude: getCenterCoordinate().latitude, longitude: getCenterCoordinate().longitude, zoom: Utils.defaultZoomLevel)
                
                self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            }
            
        }
        
    }
    
    
    
    func mapView(_ mapView: GMSMapView, idleAt position: GMSCameraPosition) {
        if(self.isCameraUpdateIgnore == true){
            self.isCameraUpdateIgnore = false
            
            return
        }
        
        if(isHeatMapEnabled){
            loadHeatMapData()
        }
        
    }
    
    func loadHeatMapData() {
        var radius = getRadius()
        
        if(radius < 0.5){
            radius = 1.0
        }
        
        if(currentRadius == 0.0 || radius > (currentRadius + 0.001)){
            getNearByPassenger(radius: radius, centerLatitude: getCenterCoordinate().latitude, centerLongitude: getCenterCoordinate().longitude)
        }
        
    }
    
    func getCenterCoordinate() -> CLLocationCoordinate2D {
        let centerPoint = self.gMapView.center
        let centerCoordinate = self.gMapView.projection.coordinate(for: centerPoint)
        return centerCoordinate
    }
    
    func getTopCenterCoordinate() -> CLLocationCoordinate2D {
        // to get coordinate from CGPoint of your map
        let topCenterCoor = self.gMapView.convert(CGPoint(self.gMapView.frame.size.width / 2.0, 0), from: gMapView)
        let point = self.gMapView.projection.coordinate(for: topCenterCoor)
        return point
    }
    
    func getRadius() -> CLLocationDistance {
        
        let centerCoordinate = getCenterCoordinate()
        
        let centerLocation = CLLocation(latitude: centerCoordinate.latitude, longitude: centerCoordinate.longitude)
        let topCenterCoordinate = self.getTopCenterCoordinate()
        let topCenterLocation = CLLocation(latitude: topCenterCoordinate.latitude, longitude: topCenterCoordinate.longitude)
        
        let radius = (centerLocation.distance(from: topCenterLocation)) / 1000
        
        return round(radius)
    }
    
    func switchValueChanged(_ sender: Any!, andNewValue value: Bool) {
        if (value == true) {
            if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                self.statusSwitch.dotColor = UIColor(hex: 0x009900)
            }else{
                statusSwitch.dotColor = UIColor(hex: 0xFFFFFF)
                statusSwitch.color = UIColor(hex: 0x009900)
                statusSwitch.tintColor = UIColor(hex: 0x009900)
            }
        } else {
            if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                self.statusSwitch.dotColor = UIColor(hex: 0xFF0000)
            }else{
                statusSwitch.dotColor = UIColor(hex: 0xFFFFFF)
                statusSwitch.color = UIColor(hex: 0xFF0000)
                statusSwitch.tintColor = UIColor(hex: 0xFF0000)
            }
        }
        
        self.updateOnlineStatus(isAlertShown: true)
    }
    
    func stateChanged(onlineOfflineStatusSwitch: UISwitch) {
        
        onlineOfflineStatusSwitch.tintColor = UIColor(hex: 0xFFFFFF)
        onlineOfflineStatusSwitch.onTintColor = UIColor(hex: 0xFFFFFF)
        
        if onlineOfflineStatusSwitch.isOn {
            onlineOfflineStatusSwitch.thumbTintColor = UIColor(hex: 0xFF0000)
        } else {
            onlineOfflineStatusSwitch.thumbTintColor = UIColor(hex: 0x009900)
        }
        
        updateOnlineStatus(isAlertShown: true)
        
    }
    
    
    func updateOnlineStatus(isAlertShown: Bool){
        
        if(isDriverOnline == false){
            goOnline(isAlertShown: isAlertShown)
        }else{
            goOffline(isAlertShown: isAlertShown)
        }
    }
    
    
    func goOnline(isAlertShown: Bool){
        
        if(currentLocation != nil){
            
            let currentLocLatitude:String = self.currentLocation!.coordinate.latitude.description
            let currentLocLongitude:String = self.currentLocation!.coordinate.longitude.description
            
            if(currentLocLatitude != "" && currentLocLatitude != "0.0" && currentLocLongitude != "" && currentLocLongitude != "0.0"){
                updateStatus(offline: false,isAlertShown: isAlertShown)
            }else{
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_FOUND_TXT"))
                self.setSwitchStatusAvoidUpdate(value: false, isAnim: false)
            }
            
        }else{
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_FOUND_TXT"))
            self.setSwitchStatusAvoidUpdate(value: false, isAnim: false)
        }
    }
    
    func goOffline(isAlertShown: Bool){
        if(isDriverOnline == true){
            if(currentLocation == nil){
                currentLocation = CLLocation(latitude: 0.0,longitude: 0.0)
            }
            updateStatus(offline: true, isAlertShown: isAlertShown)
        }else{
            self.setSwitchStatusAvoidUpdate(value: false, isAnim: false)
        }
        
    }
    
    
    func updateStatus(offline:Bool, isAlertShown:Bool){
        
        var vAvailability_str:String = ""
        if(offline == false){
            vAvailability_str = "Available"
        }else{
            vAvailability_str = "Not Available"
        }
        
        let currentLocLatitude = self.currentLocation!.coordinate.latitude.description
        let currentLocLongitude = self.currentLocation!.coordinate.longitude.description
        
        
        let parameters = ["type":"updateDriverStatus", "isUpdateOnlineDate": offline == false ? "true" : "", "iDriverId": GeneralFunctions.getMemberd(),"latitude": currentLocLatitude, "longitude": currentLocLongitude, "Status": vAvailability_str]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: isAlertShown)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                self.checkStatusUpdateRespose(dict: dataDict, offline: offline, isAlertShown: isAlertShown)
                
                
            }else{
                
                self.setSwitchStatusAvoidUpdate(value: offline, isAnim: false)
                
                if(isAlertShown == true){
                    self.generalFunc.setError(uv: self)
                }
            }
            
        })
    }
    
    
    func checkStatusUpdateRespose(dict:NSDictionary, offline:Bool, isAlertShown:Bool){
        
        if(dict.get("Action") == "1"){
            
            if(dict.get(Utils.message_str) == "REQUIRED_MINIMUM_BALNCE" && isDriverOnline == false && isAlertShown == true){
                let openMinAmountReqView = OpenMinAmountReqView(uv: self, containerView: self.cntView)
                openMinAmountReqView.setHandler(handler: { (isSkipped, isOpenWallet, view, bgView) in
                    if(isOpenWallet == true){
//                        let manageWalletUv = GeneralFunctions.instantiateViewController(pageName: "ManageWalletUV") as! ManageWalletUV
//                        self.pushToNavController(uv: manageWalletUv)
                        if(self.userProfileJson.get("APP_PAYMENT_MODE").uppercased() == "CASH"){
                            let contactUsUV = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                            self.pushToNavController(uv: contactUsUV)
                        }else{
                            let manageWalletUv = GeneralFunctions.instantiateViewController(pageName: "ManageWalletUV") as! ManageWalletUV
                            self.pushToNavController(uv: manageWalletUv)
                        }
                    }
                })
                openMinAmountReqView.show(msg: dict.get("Msg"))
                
            }
//            self.HailEnableOnDriverStatus = dict.get("Enable_Hailtrip")
            if(isDriverOnline == false){
                self.setOnlineState(isAlertShown: isAlertShown)
            }else{
                self.setOfflineState(isAlertShown: isAlertShown)
            }
            
        }else{
            
            if(offline == true){
                self.setSwitchStatusAvoidUpdate(value: true, isAnim: false)
            }else{
                self.setSwitchStatusAvoidUpdate(value: false, isAnim: false)
            }
            
            let message = dict.get("message")
            
            let message_str = dict.get("message")
            
            if(isAlertShown == false){
                return
            }
            
            if(dict.get(Utils.message_str) == "SESSION_OUT"){
                
                self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    
                    self.releaseAllTask(isDismiss: true)
                    GeneralFunctions.logOutUser()
                    GeneralFunctions.restartApp(window: self.window!)
                })
                
                return
            }else if(message_str == "DO_EMAIL_PHONE_VERIFY" || message_str == "DO_PHONE_VERIFY" || message_str == "DO_EMAIL_VERIFY"){
                
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedIndex) in
                    
                    if(btnClickedIndex == 0){
                        let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
                        accountVerificationUv.isMainPage = true
                        accountVerificationUv.requestType = message_str
                        accountVerificationUv.mainScreenUv = self
                        self.pushToNavController(uv: accountVerificationUv)
                    }
                })
                
                return
            }else if(message_str == "REQUIRED_MINIMUM_BALNCE"){
//                self.generalFunc.setError(uv: self, title: "", content: dict.get("Msg"))
                let openMinAmountReqView = OpenMinAmountReqView(uv: self, containerView: self.contentView)
                openMinAmountReqView.setHandler(handler: { (isSkipped, isOpenWallet, view, bgView) in
                    if(isOpenWallet == true){
                        if(self.userProfileJson.get("APP_PAYMENT_MODE").uppercased() == "CASH"){
                            let contactUsUV = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                            self.pushToNavController(uv: contactUsUV)
                        }else{
                            let manageWalletUv = GeneralFunctions.instantiateViewController(pageName: "ManageWalletUV") as! ManageWalletUV
                            self.pushToNavController(uv: manageWalletUv)
                        }
                    }
                })
                openMinAmountReqView.show(msg: dict.get("Msg"))
                
                return
            }else if(message_str == "LBL_INACTIVE_CARS_MESSAGE_TXT"){
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: message_str), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT").uppercased() , nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT").uppercased(), completionHandler: { (btnClickedId) in
                    
                    if(btnClickedId == 1){
                        let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                        self.pushToNavController(uv: contactUsUv)
                    }
                })
                
                return
            }
            
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: message, key: message))
            
        }
    }
    
    func setOnlineState(isAlertShown:Bool){
        
        DispatchQueue.main.async {
            Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ONLINE_HEADER_TXT"), uv: self)
            self.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GO_OFFLINE_TXT").uppercased()
            
            self.setSwitchStatusAvoidUpdate(value: true, isAnim: false)
        }
        
        GeneralFunctions.saveValue(key: "IS_DRIVER_ONLINE", value: "true" as AnyObject)
        isDriverOnline = true
        
        
        addNotifyOnPassengerRequested()
        
        if(configPubNub != nil){
            configPubNub?.subscribeToCabReqChannel()
        }
        
        if(updateDriverStatus == nil){
            updateDriverStatus = UpdateDriverStatus(uv: self)
        }
        updateDriverStatus.isOnline = true
        updateDriverStatus.scheduleDriverUpdate()
        
        updateLocationToPubNub()
        
        
    }
    
    func setOfflineState(isAlertShown:Bool){
        
        DispatchQueue.main.async {
            Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_OFFLINE_HEADER_TXT"), uv: self)
            self.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GO_ONLINE_TXT").uppercased()
            
            self.setSwitchStatusAvoidUpdate(value: false, isAnim: false)
        }
        
        GeneralFunctions.saveValue(key: "IS_DRIVER_ONLINE", value: "false" as AnyObject)
        
        isDriverOnline = false
        
        
        if(configPubNub != nil){
            configPubNub?.unSubscribeToCabReqChannel()
        }
        
        updateDriverStatus.isOnline = false
        updateDriverStatus.stopFrequentUpdate()
        
       
        
    }
    
    func setSwitchStatus(value:Bool, isAnim:Bool){
        DispatchQueue.main.async {
            self.statusSwitch.configSwitchState(value, animated: isAnim)
        }
    }
    
    func setSwitchStatusAvoidUpdate(value:Bool, isAnim:Bool){
        DispatchQueue.main.async {
            self.statusSwitch.configSwitchStateAvoidUpdate(value, animated: isAnim)
            
            if (value == true) {
                if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                    self.statusSwitch.dotColor = UIColor(hex: 0x009900)
                }else{
                    self.statusSwitch.dotColor = UIColor(hex: 0xFFFFFF)
                    self.statusSwitch.color = UIColor(hex: 0x009900)
                    self.statusSwitch.tintColor = UIColor(hex: 0x009900)
                }
            } else {
                if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                    self.statusSwitch.dotColor = UIColor(hex: 0xFF0000)
                }else{
                    self.statusSwitch.dotColor = UIColor(hex: 0xFFFFFF)
                    self.statusSwitch.color = UIColor(hex: 0xFF0000)
                    self.statusSwitch.tintColor = UIColor(hex: 0xFF0000)
                }
            }
        }
//        DispatchQueue.main.async {
//            self.statusSwitch.configSwitchStateAvoidUpdate(value, animated: isAnim)
//
//            if (value == true) {
//                self.statusSwitch.dotColor = UIColor(hex: 0x009900)
//            } else {
//                self.statusSwitch.dotColor = UIColor(hex: 0xFF0000)
//            }
//        }
        
    }
    
    func openManageProfile(isOpenEditProfile: Bool){
        let manageProfileUv = GeneralFunctions.instantiateViewController(pageName: "ManageProfileUV") as! ManageProfileUV
        manageProfileUv.isOpenEditProfile = isOpenEditProfile
        //        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageProfileUv, animated: true)
        self.pushToNavController(uv: manageProfileUv)
    }
    
    func getNearByPassenger(radius:Double, centerLatitude:Double, centerLongitude:Double){
        
        if(task_update_heatMapData != nil){
            task_update_heatMapData!.cancel()
        }
        
        let parameters = ["type":"loadPassengersLocation", "Radius": "\(radius)", "iMemberId": GeneralFunctions.getMemberd(),"Latitude": "\(centerLatitude)", "Longitude": "\(centerLongitude)"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    
                    self.checkNearByPassengerDataResponse(radius: radius, dict: dataDict)
                }else{
                    //                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
            
        })
        
        self.task_update_heatMapData = exeWebServerUrl
        
    }
    
    func checkNearByPassengerDataResponse(radius:Double,dict:NSDictionary){
        
        if(self.isHeatMapEnabled == false){
            gMapView.clear()
            
            onlineData.removeAll()
            historyData.removeAll()
            dtaCircleHeatMap.removeAll()
            currentRadius = 0
            
            return
        }
        
        if(dict.get("Action")  == "1"){
            self.currentRadius = radius
            let message = dict.get("message")
            
            var list = [GMUWeightedLatLng]()
            var onlineList = [GMUWeightedLatLng]()
            if(message != ""){
                let message_arr = dict.getArrObj("message")
                
                for i in 0 ..< message_arr.count {
                    let tempItem = message_arr[i] as! NSDictionary
                    
                    let type = tempItem.get("Type")
                    
                    //                    print("Type::\(type)")
                    
                    let latitude = Double(tempItem.get("Latitude"))
                    let longitude = Double(tempItem.get("Longitude"))
                    
                    let coords = GMUWeightedLatLng(coordinate: CLLocationCoordinate2DMake(latitude!, longitude!), intensity: 1.0)
                    
                    //                    let loc = CLLocation(latitude: latitude.doubleValue, longitude: longitude.doubleValue)
                    //
                    //                    let circle = GMSMarker()
                    //                    let redView = UIView(frame: CGRect(x: 0, y: 0, width: 20, height: 20))
                    //                    redView.backgroundColor = Color.red.withAlphaComponent(0.55)
                    //
                    //                    let greenView = UIView(frame: CGRect(x: 0, y: 0, width: 20, height: 20))
                    //                    greenView.backgroundColor = Color.green.withAlphaComponent(0.55)
                    //
                    //                    Utils.createRoundedView(view: redView, borderColor: UIColor.clear, borderWidth: 0)
                    //                    Utils.createRoundedView(view: greenView, borderColor: UIColor.clear, borderWidth: 0)
                    //
                    //                    circle.position = loc.coordinate
                    
                    if(type == "Online"){
                        
                        onlineList.append(coords)
                    }else{
                        
                        
                        
                        list.append(coords)
                    }
                    
                }
                
                heatmapLayer = GMUHeatmapTileLayer()
                heatmapLayer.gradient = GMUGradient(colors: gradientColors,
                                                    startPoints: gradientStartPoints as [NSNumber],
                                                    colorMapSize: 256)
//                heatmapLayer.opacity = 1
//                heatmapLayer.radius = 50
                self.heatmapLayer.weightedData = list
                heatmapLayer.map = gMapView
                onlineHeatmapLayer = GMUHeatmapTileLayer()
                onlineHeatmapLayer.gradient = GMUGradient(colors: onlineGradientColors,
                                                          startPoints: onlineGradientStartPoints as [NSNumber],
                                                          colorMapSize: 256)
//                onlineHeatmapLayer.opacity = 1
//                onlineHeatmapLayer.radius = 50
                self.onlineHeatmapLayer.weightedData = onlineList
                onlineHeatmapLayer.map = gMapView
                self.gMapView.setNeedsDisplay()
            }
            
            
        }
        
        
    }
    
    func updateLocationToPubNub(){
        if(isDriverOnline == true && currentLocation != nil && configPubNub != nil){
            configPubNub?.publishMsg(channelName: GeneralFunctions.getLocationUpdateChannel(), content: GeneralFunctions.buildLocationJson(location: currentLocation!))
        }
    }
    
    
    func goToMyLoc(sender: UITapGestureRecognizer) {
        
        if(currentLocation == nil){
            return
        }
        
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: currentLocation!.coordinate.latitude,
                                              longitude: currentLocation!.coordinate.longitude, zoom: currentZoomLevel)
        
        self.gMapView.animate(to: camera)
        
    }
    
    func loadAvailableCar(){
        carListDataArrList.removeAll()
        
        let parameters = ["type":"LoadAvailableCars", "iDriverId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    self.carListDataArrList.removeAll()
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.carListDataArrList += [dataTemp]
                    }
                    
                    let totalVehicleCount = self.carListDataArrList.count == 0 ? 1 : self.carListDataArrList.count
                    
                    self.selectCarView = self.generalFunc.loadView(nibName: "SelectCarDesign", uv: self, isWithOutSize: true)
                    
                    self.selectCarView.frame.size = CGSize(width: Application.screenSize.width > 370 ? 360 : (Application.screenSize.width - 50), height: ((CGFloat(totalVehicleCount) * 70) + 200) > self.contentView.frame.height ? (self.contentView.frame.height - 100) : ((CGFloat(totalVehicleCount) * 70) + 200))
                    
                    _ = ((self.selectCarView.frame.height / 2) - (self.contentView.frame.height / 2)) >= 0 ? ((self.selectCarView.frame.height / 2) - (self.contentView.frame.height / 2)) : self.contentView.bounds.midY
                    
                    self.selectCarView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY )
                    
                    
                    self.selectCarBGView = UIView()
                    self.selectCarBGView.frame = self.cntView.frame
                    self.cntView.addSubview(self.selectCarBGView)
                    self.cntView.addSubview(self.selectCarView)
                    
                    self.selectCarBGView.alpha = 0
                    self.selectCarView.alpha = 0
    
                    UIView.animate(
                        withDuration: 0.5,
                        delay: 0,
                        options: .curveEaseInOut,
                        animations: {
                            self.selectCarBGView.alpha = 0.4
                            self.selectCarView.alpha = 1
                            
                    }
                    )
                    GeneralFunctions.setImgTintColor(imgView: self.selectCarHImgView, color: UIColor.UCAColor.AppThemeColor)
                    self.selectCarHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_CAR_TXT")
                    self.manageVehiclesLbl.text = self.generalFunc.getLanguageLabel(origValue: "Manage Vehicles", key: "LBL_MANAGE_VEHICLES").uppercased()
                    self.manageVehiclesLbl.setClickDelegate(clickDelegate: self)
                    
                    self.addNewVehicleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_VEHICLES").uppercased()
                    self.addNewVehicleLbl.setClickDelegate(clickDelegate: self)
                    
                    self.selectCarBGView.backgroundColor = UIColor.black
                    self.selectCarBGView.alpha = 0.4
                    self.selectCarView.layer.shadowOpacity = 0.5
                    self.selectCarView.layer.shadowOffset = CGSize(width: 0, height: 3)
                    self.selectCarView.layer.shadowColor = UIColor.black.cgColor
                    self.selectCarView.layer.cornerRadius = 10
                    self.selectCarView.layer.masksToBounds = true
                    
                    self.selectCarTableView.dataSource = self
                    self.selectCarTableView.delegate = self
                    self.selectCarTableView.register(CountryListTVCell.self, forCellReuseIdentifier: "SelectCarListTVCell")
                    self.selectCarTableView.register(UINib(nibName: "SelectCarListTVCell", bundle: nil), forCellReuseIdentifier: "SelectCarListTVCell")
                    self.selectCarTableView.tableFooterView = UIView()
                    self.selectCarTableView.reloadData()
                    
                    let selectCarBgTapGue = UITapGestureRecognizer()
                    selectCarBgTapGue.addTarget(self, action: #selector(self.removeSelectCarView))
                    self.selectCarBGView.addGestureRecognizer(selectCarBgTapGue)
                    
                }else{
                    
                    let alertMsgLbl = dataDict.get("message")
                    
                    if alertMsgLbl == "LBL_INACTIVE_CARS_MESSAGE_TXT"{
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: alertMsgLbl), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT").uppercased() , nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT").uppercased(), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 1){
                                let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                                self.pushToNavController(uv: contactUsUv)
                            }
                        })
                        return
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: alertMsgLbl))
                    }
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    }
    
    func removeSelectCarView(){
        self.selectCarBGView.removeFromSuperview()
        self.selectCarView.removeFromSuperview()
    }
    
    func requestChangeCar(iDriverVehicleId:String, vLicencePlateNo:String, vMake:String, vModel:String){
        
        let parameters = ["type":"SetDriverCarID", "iDriverId": GeneralFunctions.getMemberd(), "iDriverVehicleId": iDriverVehicleId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.carNumLbl.text = vLicencePlateNo
                    self.carNameLBl.text = vMake + " " + vModel
                    Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_INFO_UPDATED_TXT"), uv: self)
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let item = self.carListDataArrList[indexPath.item]
        removeSelectCarView()
        
        self.requestChangeCar(iDriverVehicleId: item.get("iDriverVehicleId"), vLicencePlateNo: item.get("vLicencePlate"), vMake: item.get("vMake"), vModel: item.get("vTitle"))
        
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.carListDataArrList.count
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let cell = tableView.dequeueReusableCell(withIdentifier: "SelectCarListTVCell", for: indexPath) as! SelectCarListTVCell
        
        let item = self.carListDataArrList[indexPath.item]
        
        cell.carNameLbl.text = item.get("vMake") + " " + item.get("vTitle")
        
        if(item.get("DriverSelectedVehicleId") == item.get("iDriverVehicleId")){
            cell.selectionImgView.image = UIImage(named: "ic_select_true")
            GeneralFunctions.setImgTintColor(imgView: cell.selectionImgView, color: UIColor.UCAColor.AppThemeColor)
        }else{
            cell.selectionImgView.image = UIImage(named: "ic_select_false")
            GeneralFunctions.setImgTintColor(imgView: cell.selectionImgView, color: UIColor(hex: 0xd3d3d3))
        }
        
//        GeneralFunctions.setImgTintColor(imgView: cell.selectionImgView, color: UIColor.UCAColor.AppThemeColor)
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        
        return cell
    }
    
    
    @IBAction func unwindToMainScreen(_ segue:UIStoryboardSegue) {
        
    }
}
