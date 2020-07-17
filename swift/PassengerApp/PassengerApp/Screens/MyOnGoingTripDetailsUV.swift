//
//  MyOnGoingTripDetailsUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 18/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps

class MyOnGoingTripDetailsUV: UIViewController, UITableViewDelegate, UITableViewDataSource, OnTaskRunCalledDelegate, OnDirectionUpdateDelegate {

    
    var MENU_CALL = "0"
    var MENU_MSG = "1"
    var MENU_LIVE_TRACK_OR_PROGRESS = "2"
    var MENU_EMERGENCY = "3"
    var MENU_CANCEL = "4"
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var detailBottomVIew: UIView!
    @IBOutlet weak var providerImgView: UIImageView!
    @IBOutlet weak var sourceAddLbl: MyLabel!
    @IBOutlet weak var providerDetailViewHeight: NSLayoutConstraint!
    @IBOutlet weak var providerNameLbl: MyLabel!
    @IBOutlet weak var bottomPointViewHeight: NSLayoutConstraint!
    @IBOutlet weak var providerDetailView: UIView!
    @IBOutlet weak var ratingView: RatingView!
    @IBOutlet weak var statusTitleLbl: MyLabel!
    @IBOutlet weak var gMapContainerView: UIView!
    @IBOutlet weak var tripEtaLbl: MyLabel!
    
    @IBOutlet weak var tableView: UITableView!
    
    var gMapView:GMSMapView!
    
    var dataDict:NSDictionary!
    
    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var dataArrList = [NSDictionary]()
    
    var cntView:UIView!
    var menu:BTNavigationDropdownMenu!
    
    var configPubNub:ConfigPubNub?
    
    var isDriverArrived = false
    var isTripStarted = false
    var isTripFinished = false
    
    var assignedDriverMarker:GMSMarker!
    var assignedDriverRotatedLocation:CLLocation!
    var assignedDriverLocation:CLLocation!
    
    var updateFreqDriverLocTask:UpdateFreqTask!
    var driverDetails:NSDictionary!
    var eType = ""
    
    var updateDirection:UpdateDirections!
    
    var userProfileJson:NSDictionary!
    
    var isPageLoaded = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.addBackBarBtn()
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        cntView = self.generalFunc.loadView(nibName: "MyOnGoingTripDetailsScreenDesign", uv: self, contentView: contentView)
        
        self.contentView.addSubview(cntView)
        
        setData()
        
        self.tableView.delegate = self
        self.tableView.bounces = false
        self.tableView.dataSource = self
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "MyOnGoingTripDetailsTVCell", bundle: nil), forCellReuseIdentifier: "MyOnGoingTripDetailsTVCell")
        self.tableView.contentInset = UIEdgeInsets(top: 8, left: 0, bottom: 8, right: 0)
        
        
        self.navigationItem.rightBarButtonItem =  UIBarButtonItem(image: UIImage(named: "ic_menu")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openPopUpMenu))
        
        
        if(self.getPubNubConfig().uppercased() == "YES"){
            
            GeneralFunctions.postNotificationSignal(key: ConfigPubNub.pauseInst_key, obj: self)
            
            configPubNub = ConfigPubNub()
            configPubNub!.iDriverId = dataDict.get("iDriverId")
            configPubNub!.iTripId = self.dataDict.get("iTripId")
            configPubNub!.buildPubNub()
        }
        
        if(dataDict.get("driverStatus") == "Arrived"){
            self.isDriverArrived = true
        }
        
        if(dataDict.get("driverStatus") == "On Going Trip"){
            self.isDriverArrived = true
            self.isTripStarted = true
        }
        
        if(self.configPubNub == nil){
            
            let DRIVER_LOC_FETCH_TIME_INTERVAL = GeneralFunctions.parseDouble(origValue: 5, data: userProfileJson.get("DRIVER_LOC_FETCH_TIME_INTERVAL"))
            updateFreqDriverLocTask = UpdateFreqTask(interval: DRIVER_LOC_FETCH_TIME_INTERVAL)
            updateFreqDriverLocTask.currInst = updateFreqDriverLocTask
            updateFreqDriverLocTask.setTaskRunListener(onTaskRunCalled: self)
        }
        
        if((isDriverArrived == false || dataDict.get("eFareType") == "Regular") && configPubNub != nil){
            subscribeToDriverLocChannel()
        }
        
        self.cntView.isHidden = true
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.checkJobEndData(sender:)), name: NSNotification.Name(rawValue: ConfigPubNub.TRIP_COMPLETE_NOTI_OBSERVER_KEY), object: nil)

        self.addDriverNotificationObserver()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            cntView.frame = self.contentView.frame
            
            let bottomPointImg = UIImage(named: "ic_bottom_anchor_point", in: Bundle(for: MyOnGoingTripDetailsUV.self), compatibleWith: self.traitCollection)
            
            let iv = UIImageView(image: bottomPointImg)
            
            detailBottomVIew.backgroundColor = UIColor(patternImage: UIImage(named: "ic_bottom_anchor_point")!)
            bottomPointViewHeight.constant = iv.frame.height
            
            getData()
            
            isPageLoaded = true
        }
    }
    
    override func closeCurrentScreen() {
        releaseAllTask()
        if(self.menu != nil){
            self.menu.hideMenu()
        }
        super.closeCurrentScreen()
    }
    
    deinit {
        releaseAllTask()
    }
    
    
    func checkJobEndData(sender: NSNotification){
        let userInfo = sender.userInfo
        let msgData = (userInfo!["body"] as! String).getJsonDataDict()
        
//        let msgStr = msgData.get("Message")
        
        
        if(dataDict.get("iTripId") != msgData.get("iTripId")){
            return
        }
        
        self.releaseAllTask()
        if(self.menu != nil){
            self.menu.hideMenu()
        }
        self.performSegue(withIdentifier: "unwindToMyOnGoingTripsScreen", sender: self)
        
    }
    
    func subscribeToDriverLocChannel(){
        var channels =  [String]()
        channels += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER+self.dataDict.get("iDriverId")]
        if(configPubNub != nil){
            self.configPubNub?.subscribeToChannels(channels: channels)
        }
    }
    
    func unSubscribeToDriverLocChannel(){
        var channels =  [String]()
        channels += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER+self.dataDict.get("iDriverId")]
        if(configPubNub != nil){
            self.configPubNub?.subscribeToChannels(channels: channels)
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING") + "# " + Configurations.convertNumToAppLocal(numStr: dataDict.get("vRideNo"))
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING") + "# " + Configurations.convertNumToAppLocal(numStr: dataDict.get("vRideNo"))
        
        self.statusTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROGRESS_HINT")
        self.statusTitleLbl.textColor = UIColor.UCAColor.AppThemeColor
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func addDriverNotificationObserver(){
        NotificationCenter.default.addObserver(self, selector: #selector(self.driverCallBackReceived(sender:)), name: NSNotification.Name(rawValue: Utils.driverCallBackNotificationKey), object: nil)
    }
    
    func driverCallBackReceived(sender: NSNotification){
        let userInfo = sender.userInfo
        let msgData = (userInfo!["body"] as! String).getJsonDataDict()
        
        let msgStr = msgData.get("Message")
        
        
        if(dataDict.get("iTripId") != msgData.get("iTripId")){
            return
        }
        Utils.resetAppNotifications()
        Utils.closeKeyboard(uv: self)
        
        if(msgStr == "TripStarted"){
            if(self.isTripStarted == true){
                return
            }
            self.isTripStarted = true
            
//            LocalNotification.dispatchlocalNotification(with: "", body: (GeneralFunctions()).getLanguageLabel(origValue: "", key: "LBL_START_TRIP_DIALOG_TXT"), at: Date().addedBy(seconds: 1))
            
//            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_START_TRIP_DIALOG_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
            
                self.getData()
//            })
            
        }else if(msgStr == "TripCancelledByDriver" || msgStr == "TripEnd"){
            
            if(self.isTripFinished == true){
                return
            }
            self.isTripFinished = true
            
//            LocalNotification.dispatchlocalNotification(with: "", body: (GeneralFunctions()).getLanguageLabel(origValue: "", key: msgStr == "TripCancelledByDriver" ? "LBL_CANCELED_TXT" : "LBL_FINISHED_TXT"), at: Date().addedBy(seconds: 1))
            
            self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: msgStr == "TripCancelledByDriver" ? "LBL_CANCELED_TXT" : "LBL_FINISHED_TXT"), content: msgStr == "TripCancelledByDriver" ? (self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " " + msgData.get("Reason") + " " + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP_BY_DRIVER_MSG_SUFFIX")) : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_END_TRIP_DIALOG_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                
//                let window = Application.window
//                
//                let getUserData = GetUserData(uv: self, window: window!)
//                getUserData.getdata()
                
                self.releaseAllTask()
                self.performSegue(withIdentifier: "unwindToMyOnGoingTripsScreen", sender: self)
                
            })
        }
    }
    
    
    func initializeMenu(){
        
        var items = [NSDictionary]()
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CALL_TXT"),"ID" : MENU_CALL] as NSDictionary)
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MESSAGE_TXT"),"ID" : MENU_MSG] as NSDictionary)
        
        if(self.isDriverArrived == false || dataDict.get("eFareType") == "Regular"){
            items.append(["Title" : (self.gMapView == nil ? self.generalFunc.getLanguageLabel(origValue: "Live Track", key: "LBL_LIVE_TRACK_TXT") : self.generalFunc.getLanguageLabel(origValue: "JOB PROGRESS", key: "LBL_PROGRESS_HINT")),"ID" : MENU_LIVE_TRACK_OR_PROGRESS] as NSDictionary)
        }
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "SOS", key: "LBL_EMERGENCY_SOS_TXT"),"ID" : MENU_EMERGENCY] as NSDictionary)
        
        
        if(isTripStarted == false){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP"),"ID" : MENU_CANCEL] as NSDictionary)
        }
        
        
        if(self.menu == nil){
        
            menu = BTNavigationDropdownMenu(navigationController: self.navigationController, title: "", items: items)
            
            menu.cellHeight = 65
            menu.cellBackgroundColor = UIColor.UCAColor.AppThemeColor.lighter(by: 10)
            menu.cellSelectionColor = UIColor.UCAColor.AppThemeColor
            menu.cellTextLabelColor = UIColor.UCAColor.AppThemeTxtColor
            menu.cellTextLabelFont = UIFont(name: "Roboto-Light", size: 20)
            menu.cellSeparatorColor = UIColor.UCAColor.AppThemeColor
            
            if(Configurations.isRTLMode()){
                menu.cellTextLabelAlignment = NSTextAlignment.right
            }else{
                menu.cellTextLabelAlignment = NSTextAlignment.left
            }
            menu.arrowPadding = 15
            menu.animationDuration = 0.5
            menu.maskBackgroundColor = UIColor.black
            menu.maskBackgroundOpacity = 0.5
            menu.menuStateHandler = { (isMenuOpen: Bool) -> () in
                
                //                if(isMenuOpen){
                //                    self.rightButton.setBackgroundImage(nil, for: .normal, barMetrics: .default)
                //
                //                }else{
                //                    self.rightButton.setBackgroundImage(UIImage(color : UIColor.UCAColor.AppThemeColor.lighter(by: 10)!), for: .normal, barMetrics: .default)
                //                }
                
            }
            
            menu.didSelectItemAtIndexHandler = {(indexID: String) -> () in
                
                switch indexID {
                case self.MENU_MSG:
                    let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
                    chatUV.receiverId = self.dataDict.get("iDriverId")
                    chatUV.receiverDisplayName = self.dataDict.get("driverName")
                    chatUV.assignedtripId = self.dataDict.get("iTripId")
                    chatUV.pPicName = self.dataDict.get("driverImage")
                    
                    self.pushToNavController(uv:chatUV, isDirect: true)
                    break
                case self.MENU_CALL:
                    UIApplication.shared.openURL(NSURL(string:"telprompt:" + self.dataDict.get("driverMobile"))! as URL)
                    break
                case self.MENU_LIVE_TRACK_OR_PROGRESS:
                    if(self.gMapView != nil){
                        self.removeDriverTracking()
                    }else{
                        self.gMapContainerView.isHidden = false
                        
                        
                        if(self.assignedDriverLocation == nil){
                            self.assignedDriverLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.dataDict.get("driverLatitude")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.dataDict.get("driverLongitude")))
                        }
                        
                        
                        let camera = GMSCameraPosition.camera(withLatitude: self.assignedDriverLocation.coordinate.latitude, longitude: self.assignedDriverLocation.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                        let gMapView = GMSMapView.map(withFrame: CGRect(x: 0, y: 0, width: self.gMapContainerView.frame.width, height: self.gMapContainerView.frame.height), camera: camera)
                        
                        self.gMapView = gMapView
                        
                        
                        self.gMapContainerView.addSubview(gMapView)
                        self.gMapContainerView.backgroundColor = UIColor.black
                        self.statusTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "Live Track", key: "LBL_LIVE_TRACK_TXT")
                        
                        
                        CATransaction.begin()
                        CATransaction.setAnimationDuration(1.0)
                        self.gMapView.animate(with: (GMSCameraUpdate.setCamera(GMSCameraPosition.camera(withLatitude: self.assignedDriverLocation.coordinate.latitude,longitude: self.assignedDriverLocation.coordinate.longitude, zoom: Utils.defaultZoomLevel))))
                        CATransaction.commit()
                        
                        self.updateAssignedDriverMarker(driverLocation: self.assignedDriverLocation)
                    }
                    break
                case self.MENU_EMERGENCY:
                    let confirmEmergencyTapUV = GeneralFunctions.instantiateViewController(pageName: "ConfirmEmergencyTapUV") as! ConfirmEmergencyTapUV
                    confirmEmergencyTapUV.iTripId = self.dataDict.get("iTripId")
                    self.pushToNavController(uv: confirmEmergencyTapUV)
                    break
                case self.MENU_CANCEL:
                     self.cancelBooking()
                    break
                default:
                    break
                }
            }
            
        }else{
            menu.updateItems(items)
        }
    
    }
    
    func startDriverTracking(){
        let tStartLat = driverDetails.get("tStartLat")
        let tStartLong = driverDetails.get("tStartLong")
        
        let toLoc = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tStartLat), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tStartLong))
        updateDirection = UpdateDirections(uv: self, gMap: GMSMapView(), fromLocation: self.assignedDriverLocation, destinationLocation: toLoc, isCurrentLocationEnabled: false)
        updateDirection.onDirectionUpdateDelegate = self
        updateDirection.setCurrentLocEnabled(isCurrentLocationEnabled: false)
        updateDirection.scheduleDirectionUpdate()

    }
    
    func stopTracking(){
        if(self.updateDirection != nil){
            self.updateDirection.onDirectionUpdateDelegate = nil
            self.updateDirection.gMap = nil
            self.updateDirection = nil
        }
        
    }
    
    func onDirectionUpdate(directionResultDict: NSDictionary) {
        
        if(directionResultDict.get("status").uppercased() != "OK" || directionResultDict.getArrObj("routes").count == 0){
            return
        }
        
        let routesArr = directionResultDict.getArrObj("routes")
        let legs_arr = (routesArr.object(at: 0) as! NSDictionary).getArrObj("legs")
        let steps_arr = (legs_arr.object(at: 0) as! NSDictionary).getArrObj("steps")
        //                let start_address = (legs_arr.object(at: 0) as! NSDictionary).get("start_address")
        let end_address = (legs_arr.object(at: 0) as! NSDictionary).get("end_address")
        let distance_value = (legs_arr.object(at: 0) as! NSDictionary).getObj("distance").get("value")
        let time_str = (legs_arr.object(at: 0) as! NSDictionary).getObj("duration").get("text")
        
        var distance_final = GeneralFunctions.parseDouble(origValue: 0.0, data: distance_value)
        
        if(self.userProfileJson != nil && self.userProfileJson.get("eUnit") != "KMs"){
            distance_final = distance_final * 0.000621371
        }else{
            distance_final = distance_final * 0.00099999969062399994
        }
        
        distance_final = distance_final.roundTo(places: 2)

        
        var distance_str = ""
        
        if(self.userProfileJson != nil && self.userProfileJson.get("eUnit") != "KMs"){
            distance_str = "\(String(format: "%.02f", distance_final)) \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MILE_DISTANCE_TXT"))"
        }else{
            distance_str = "\(String(format: "%.02f", distance_final)) \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_KM_DISTANCE_TXT"))"
        }
        
        
        tripEtaLbl.isHidden = (gMapView == nil) ? true : (gMapView.isHidden == true ? true : false)
        tripEtaLbl.text = "\(time_str) \(self.generalFunc.getLanguageLabel(origValue: "to reach", key: "LBL_TO_REACH")) & \(distance_str) \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_AWAY"))"
//        tripEtaLbl.fitText()
    }
    
    func removeDriverTracking(){
        self.gMapContainerView.isHidden = true
        if(tripEtaLbl != nil){
            self.tripEtaLbl.isHidden = true
        }
        
        if(self.gMapView != nil){
            self.gMapView.removeFromSuperview()
            self.gMapView = nil
        }
        self.statusTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "JOB PROGRESS", key: "LBL_PROGRESS_HINT")
    }
    
    func updateDriverLocation(iDriverId:String, latitude:String, longitude:String){
        
        if(self.gMapView != nil){
            updateAssignedDriverMarker(driverLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: latitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: longitude)))
        }
    }
    
    func onTaskRun(currInst: UpdateFreqTask) {
        checkDriverLocation()
    }
    
    func checkDriverLocation(){
        
        if(self.gMapView == nil){
            return
        }
        
        let parameters = ["type":"getDriverLocations", "iUserId": GeneralFunctions.getMemberd(), "iDriverId": self.dataDict.get("iDriverId"), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let vLatitude = dataDict.get("vLatitude")
                    let vLongitude = dataDict.get("vLongitude")
                    let vTripStatus = dataDict.get("vTripStatus")
                    
                    if(vTripStatus == "Arrived" && self.isDriverArrived == false){
                        self.setDriverArrivedStatus()
                    }
                    
                    if(vTripStatus == "Arrived"){
                        self.isDriverArrived = true
                        self.isTripStarted = true
                    }
                    
                    if(vTripStatus == "On Going Trip"){
                        self.isDriverArrived = true
                        self.isTripStarted = true
                    }
                    
                    if(vLatitude != "" && vLatitude != "0.0" && vLatitude != "-180.0" && vLongitude != "" && vLongitude != "0.0" && vLongitude != "-180.0"){
                        
                        self.updateAssignedDriverMarker(driverLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: vLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: vLongitude)))
                    }
                }else{
                    
                    
                    //                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    func updateAssignedDriverMarker(driverLocation:CLLocation){
        
        self.assignedDriverLocation = driverLocation
        
        if(self.assignedDriverMarker == nil){
            let driverMarker = GMSMarker()
            self.assignedDriverMarker = driverMarker
        }
        
        var zoom:Float = self.gMapView.camera.zoom
        if(assignedDriverRotatedLocation == nil){
            zoom = Utils.defaultZoomLevel
        }
        
        
        let camera = GMSCameraPosition.camera(withLatitude: self.assignedDriverLocation.coordinate.latitude,
                                              longitude: self.assignedDriverLocation.coordinate.longitude, zoom: zoom)
//        self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
        
        CATransaction.begin()
        CATransaction.setAnimationDuration(1.0)
        self.gMapView.animate(with: GMSCameraUpdate.setCamera(camera))
        CATransaction.commit()
        
        var rotationAngle:Double = -1
        if(assignedDriverRotatedLocation != nil){
            rotationAngle = assignedDriverRotatedLocation.bearingToLocationDegrees(destinationLocation: driverLocation, currentRotation: assignedDriverMarker.rotation)
            //            Utils.printLog(msgData: "rotationAngle0:\(rotationAngle)")
            if(rotationAngle != -1){
                assignedDriverRotatedLocation = driverLocation
            }
        }else{
            assignedDriverRotatedLocation = driverLocation
        }
        
        if eType == Utils.cabGeneralType_UberX{
            rotationAngle = 0
        }
        //        Utils.printLog(msgData: "rotationAngle1:\(rotationAngle)")
        Utils.updateMarker(marker: assignedDriverMarker, googleMap: self.gMapView, coordinates: driverLocation.coordinate, rotationAngle: rotationAngle, duration: 1.0)
        
        var iconId = "ic_driver_car_pin"
        if(eType == "Bike"){
            iconId = "ic_bike"
        }else if(eType == "Cycle"){
            iconId = "ic_cycle"
        }
        
        if (eType == Utils.cabGeneralType_UberX){
            let providerView = self.getProviderMarkerView(providerImage: UIImage(named: "ic_provider_general")!)
            assignedDriverMarker.icon = UIImage(view: providerView)
        
            (providerView.subviews[1] as! UIImageView).sd_setImage(with: URL(string: ""), placeholderImage: UIImage(named: "ic_provider_general"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                self.assignedDriverMarker.icon = UIImage(view: providerView)
            })
            assignedDriverMarker.groundAnchor = CGPoint(x: 0.5, y: 1.0)
        }else{
            assignedDriverMarker.icon = UIImage(named: iconId)
            assignedDriverMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        }
        assignedDriverMarker.map = self.gMapView
        assignedDriverMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        assignedDriverMarker.isFlat = true
        
        if(self.assignedDriverLocation != nil && self.updateDirection == nil && self.isDriverArrived == false){
            startDriverTracking()
        }
    
    }
    
    func getProviderMarkerView(providerImage:UIImage) -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "ProviderMapMarkerView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        view.frame.size = CGSize(width: 64, height: 100)
        
        GeneralFunctions.setImgTintColor(imgView: view.subviews[0] as! UIImageView, color: UIColor.UCAColor.AppThemeColor)
        
        view.subviews[1].layer.cornerRadius = view.subviews[1].frame.width / 2
        view.subviews[1].layer.masksToBounds = true
        let providerImgView = view.subviews[1] as! UIImageView
        providerImgView.image = providerImage
        
        return view
    }
    

    func setDriverArrivedStatus(){
        if(self.isDriverArrived == false){
            
            self.removeDriverTracking()
            self.getData()
//            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVE"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
//                
//            })
        }
        
        if(configPubNub != nil && dataDict.get("eFareType") != "Regular"){
            unSubscribeToDriverLocChannel()
        }
        
        if(tripEtaLbl != nil){
            self.tripEtaLbl.isHidden = true
        }
        self.stopTracking()
        
    }
    
    func cancelBooking(){
        self.view.endEditing(true)
        
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_CANCEL_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP_NOW"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTINUE_TRIP_TXT"), completionHandler: { (btnClickedIndex) in
            
            if(btnClickedIndex == 0){
                self.continueCancelTrip()
            }
        })

    }
    
    func continueCancelTrip(){
        self.view.endEditing(true)
        
        let parameters = ["type":"cancelTrip", "iUserId": GeneralFunctions.getMemberd(), "iDriverId": self.dataDict.get("iDriverId"), "UserType": Utils.appUserType, "iTripId": dataDict.get("iTripId")]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Your trip is successfully canceled.", key: "LBL_SUCCESS_TRIP_CANCELED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
//                        let window = Application.window
//                        
//                        let getUserData = GetUserData(uv: self, window: window!)
//                        getUserData.getdata()
                        self.releaseAllTask()
                        self.performSegue(withIdentifier: "unwindToMyOnGoingTripsScreen", sender: self)
                        
                    })
                }else{
                    
                    if(dataDict.get(Utils.message_str) == "DO_RESTART"){
//                        let window = Application.window
//                        
//                        let getUserData = GetUserData(uv: self, window: window!)
//                        getUserData.getdata()
                        self.releaseAllTask()
                        self.performSegue(withIdentifier: "unwindToMyOnGoingTripsScreen", sender: self)
                        return
                    }
                    
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    func openPopUpMenu(){
        
        initializeMenu()
        
        if(menu.isShown){
            menu.hideMenu()
            return
        }else{
            menu.showMenu()
        }
    }
    
    func getData(){
        self.dataArrList.removeAll()
        self.tableView.reloadData()
        
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        }
        loaderView.backgroundColor = UIColor.clear
        loaderView.isHidden = false
        
        self.cntView.isHidden = true
        
        let parameters = ["type":"getTripDeliveryLocations", "iTripId": dataDict.get("iTripId"), "userType": Utils.appUserType,"iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.driverDetails = dataDict.getObj(Utils.message_str).getObj("driverDetails")
                    Utils.createRoundedView(view: self.providerImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 1)
                    
                    self.providerImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(self.driverDetails.get("iDriverId"))/\(self.driverDetails.get("driverImage"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                        
                    })
                    self.providerNameLbl.textColor = UIColor.UCAColor.AppThemeColor
                    self.providerNameLbl.text = self.driverDetails.get("driverName")
                    self.ratingView.rating = GeneralFunctions.parseFloat(origValue: 0, data: self.driverDetails.get("driverRating"))
                    self.sourceAddLbl.text = self.driverDetails.get("tSaddress")
                    
                    self.sourceAddLbl.fitText()
                    
                    let extraHeight = self.sourceAddLbl.text!.height(withConstrainedWidth: Application.screenSize.width - 106, font: self.sourceAddLbl.font!) - 20
                    
                    self.providerDetailViewHeight.constant = 110 + extraHeight
                    
                    if(self.driverDetails.get("driverStatus") == "Arrived"){
                        self.removeDriverTracking()
                        self.isDriverArrived = true
                    }
                    
                    if(self.driverDetails.get("On Going Trip") == "On Going Trip"){
                        self.isDriverArrived = true
                        self.isTripStarted = true
                    }
                    self.eType = self.driverDetails.get("eType")
                    
                    let dataArr = dataDict.getObj(Utils.message_str).getArrObj("States")
                    
                    self.dataArrList.removeAll()
                    self.tableView.reloadData()
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                    }
                    
                    self.tableView.reloadData()
                    
                    
                }else{
                    _ = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
                self.cntView.isHidden = false
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
        return self.dataArrList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "MyOnGoingTripDetailsTVCell", for: indexPath) as! MyOnGoingTripDetailsTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.progressMsgLbl.text = item.get("text")
        cell.progressTimeLbl.text = item.get("time")
        cell.progressPastTimeLbl.text = item.get("time") //item.get("timediff")
        cell.noLbl.text = "\(indexPath.item + 1)"
        Utils.createRoundedView(view: cell.noView, borderColor: UIColor.clear, borderWidth: 0)
        
        cell.noView.backgroundColor = UIColor.UCAColor.AppThemeColor
        cell.noLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        cell.progressTimeLbl.textColor = UIColor.UCAColor.AppThemeColor
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
    }
    
    
    func releaseAllTask(){
        
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
        
        self.stopTracking()
        if(self.updateFreqDriverLocTask != nil){
            self.updateFreqDriverLocTask.stopRepeatingTask()
            self.updateFreqDriverLocTask.onTaskRunCalled = nil
            self.updateFreqDriverLocTask = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
    }

}
