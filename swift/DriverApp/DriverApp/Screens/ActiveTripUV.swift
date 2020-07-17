//
//  ActiveTripUV.swift
//  DriverApp
//
//  Created by NEW MAC on 29/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps

class ActiveTripUV: UIViewController, GMSMapViewDelegate, OnLocationUpdateDelegate, OnTripCanceledDelegate, AddressFoundDelegate, UITableViewDelegate, UITableViewDataSource, MyBtnClickDelegate {

    
    var MENU_USER_OR_DELIVERY_DETAIL = "0"
    var MENU_USER_CALL = "1"
    var MENU_USER_MSG = "2"
    var MENU_EMERGENCY = "3"
    var MENU_CANCEL_TRIP = "4"
    
    var MENU_SPECIAL_INS = "5"
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
    @IBOutlet weak var topDataContainerStkView: UIStackView!
//    @IBOutlet weak var navigateView: UIView!
    @IBOutlet weak var topDataContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var tripBtn: MyButton!
    @IBOutlet weak var addDestinationView: UIView!
    @IBOutlet weak var addDestinationLbl: MyLabel!
    
    @IBOutlet weak var addDestView: UIView!
    @IBOutlet weak var addDestLbl: MyLabel!
    
    @IBOutlet weak var btnIconBgView: UIView!
    @IBOutlet weak var btnIconImgView: UIImageView!
    @IBOutlet weak var rightArrowImgView: UIImageView!

    @IBOutlet weak var emeImgView: UIImageView!
    @IBOutlet weak var googleLogoImgView: UIImageView!
    
    //UFX related OutLets
    @IBOutlet weak var detailBottomVIew: UIView!
    @IBOutlet weak var senderViewHeight: NSLayoutConstraint!
    @IBOutlet weak var senderImgView: UIImageView!
    @IBOutlet weak var sourceAddLbl: MyLabel!
    @IBOutlet weak var senderNameLbl: MyLabel!
    @IBOutlet weak var bottomPointViewHeight: NSLayoutConstraint!
    @IBOutlet weak var senderDetailView: UIView!
    @IBOutlet weak var ratingView: RatingView!
    @IBOutlet weak var jobStatusTitleLbl: MyLabel!
    @IBOutlet weak var progressViewHeight: NSLayoutConstraint!
    @IBOutlet weak var progressStatusTitleLbl: MyLabel!
    @IBOutlet weak var progressBtn: MyButton!
    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var hourVLbl: MyLabel!
    @IBOutlet weak var hourHLbl: MyLabel!
    @IBOutlet weak var minuteVLbl: MyLabel!
    @IBOutlet weak var minuteHLbl: MyLabel!
    @IBOutlet weak var secHLbl: MyLabel!
    @IBOutlet weak var secVLbl: MyLabel!
    @IBOutlet weak var ufxHeaderView: UIView!
    @IBOutlet weak var ufxHeaderViewHeight: NSLayoutConstraint!
    @IBOutlet weak var progressView: UIView!
    
    // Surge Price OutLets
    @IBOutlet weak var surgePriceHLbl: MyLabel!
    @IBOutlet weak var surgePriceVLbl: MyLabel!
    @IBOutlet weak var surgePayAmtLbl: MyLabel!
    @IBOutlet weak var surgeAcceptBtn: MyButton!
    @IBOutlet weak var surgeLaterLbl: MyLabel!
    
    var surgePriceView:UIView!
    var surgePriceBGView:UIView!
    
    var dataArrList = [NSDictionary]()
    var loaderView:UIView!
    
    let generalFunc = GeneralFunctions()
    
    var isPageLoaded = false
    
    var currentLocation:CLLocation!
    var currentRotatedLocation:CLLocation!
    var currentHeading:Double = 0
    var isFirstHeadingCompleted = false
    
    var gMapView:GMSMapView!
    
//    var navView:UIView!
    var topNavView:navigationVIew!
    
    var window:UIWindow!
    var configPubNub:ConfigPubNub?
    
    var getLocation:GetLocation!
    
    var isFirstLocationUpdate = true
    
    var tripData:NSDictionary!
    
    var menu:BTNavigationDropdownMenu!
    
    var updateDriverLoc:UpdateDriverLocations!
    
    var updateDirections:UpdateDirections!
    var updateTripLocationService:UpdateTripLocationService!
    
    let driverMarker: GMSMarker = GMSMarker()
    let destinationMarker: GMSMarker = GMSMarker()
    
    let btnPanGue = UIPanGestureRecognizer()
    
    var isTripStarted = false
    
    var isTripEndPressed = false
    
    var cancelReason = ""
    var cancelComment = ""
    
    var getAddressFrmLocation:GetAddressFromLocation!
    
    var tripTaskExecuted = false
    
    var locationDialog:OpenLocationEnableView!
    
    var isDeliveryCodeEntered = false
    
    var ufxCntView:UIView!
    
    var iTripTimeId = ""
    
    var totalSecond:Double = 0
    
    var isResume = true
    
    var jobTimer:Timer!
    var latitudeList = [String]()
    var longitudeList = [String]()
    
//    var PHOTO_UPLOAD_SERVICE_ENABLE = "No"
    
//    var UFX_PHOTO_SELECT_TASK_COMPLETED = false
    var serviceImage:UIImage!
    
    var headerViewHeight:CGFloat = 347
    
    var userProfileJson:NSDictionary!
    
    var destinationOnTripLatitude = ""
    var destinationOnTripLongitude = ""
    var destinationOnTripAddress = ""
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            isPageLoaded = true
            
            if(tripData.get("REQUEST_TYPE").uppercased() != Utils.cabGeneralType_UberX.uppercased() || (tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && tripData.get("eFareType") == "Regular")){
                
                topNavView = navigationVIew(frame: CGRect(x:0, y:0, width: Application.screenSize.width, height: 95))
                topNavView.backgroundColor = UIColor.clear
                
                
                if (tripData.get("DestLocLatitude") != "" && tripData.get("DestLocLatitude") != "0"
                    && tripData.get("DestLocLongitude") != "" && tripData.get("DestLocLongitude") != "0" && (tripData.get("REQUEST_TYPE").uppercased() != Utils.cabGeneralType_UberX.uppercased() || (tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && tripData.get("eFareType") == "Regular"))) {
                    
                    topDataContainerStkView.addArrangedSubview(topNavView)
                    topDataContainerViewHeight.constant = 95
                }else{
                    let addDestView = generalFunc.loadView(nibName: "AddDestinationView", uv: self, isWithOutSize: true)
                    addDestView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: 50)
                    
                    topDataContainerStkView.addArrangedSubview(addDestView)
                    topDataContainerViewHeight.constant = 50
                }
                
                let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: 0.0)
                gMapView = GMSMapView.map(withFrame: self.googleMapContainerView.frame, camera: camera)
                //        googleMapContainerView = gMapView
                //        gMapView = GMSMapView()
                //            gMapView.isMyLocationEnabled = true
                gMapView.settings.rotateGestures = false
                gMapView.settings.tiltGestures = false
                gMapView.delegate = self
                self.googleMapContainerView.addSubview(gMapView)
            }else{
                let ufxCntView = self.generalFunc.loadView(nibName: "ActiveTripUFXScreenDesign", uv: self)
                ufxCntView.frame = self.googleMapContainerView.frame
                self.ufxCntView = ufxCntView
                self.googleMapContainerView.addSubview(ufxCntView)
            }
            
            setData()
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        window = Application.window!
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ActiveTripScreenDesign", uv: self, contentView: contentView))
        
        Utils.driverMarkersPositionList.removeAll()
        Utils.driverMarkerAnimFinished = true
        
        self.btnIconBgView.backgroundColor = UIColor.UCAColor.AppThemeColor_1
        GeneralFunctions.setImgTintColor(imgView: self.btnIconImgView, color: UIColor.UCAColor.AppThemeColor)
        Utils.createRoundedView(view: self.btnIconBgView, borderColor: Color.clear, borderWidth: 0)
        
        if(Configurations.isRTLMode()){
            var scalingTransform : CGAffineTransform!
            scalingTransform = CGAffineTransform(scaleX: -1, y: 1);
            self.rightArrowImgView.transform = scalingTransform
        }
        
        self.emeImgView.isHidden = true
        
        self.contentView.isHidden = true
        
//        self.PHOTO_UPLOAD_SERVICE_ENABLE = GeneralFunctions.getValue(key: "PHOTO_UPLOAD_SERVICE_ENABLE") == nil ? "No" : (GeneralFunctions.getValue(key: "PHOTO_UPLOAD_SERVICE_ENABLE") as! String)
        
        if(GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") != nil && (GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") as! String) == "true"){
            let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
            
            GeneralFunctions.removeValue(key: "OPEN_MSG_SCREEN")
            
            chatUV.receiverId = tripData!.get("PassengerId")
            chatUV.receiverDisplayName = self.tripData!.get("PName")
            chatUV.assignedtripId = self.tripData!.get("TripId")
            self.pushToNavController(uv:chatUV, isDirect: true)
            
        }
        
        if(tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            self.btnIconImgView.isHidden = true
            self.btnIconBgView.isHidden = true
        }
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
    }

    deinit {
        releaseAllTask()
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EN_ROUTE_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EN_ROUTE_TXT")
        
        let rightButton = UIBarButtonItem(image: UIImage(named: "ic_menu")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openPopUpMenu))
        self.navigationItem.rightBarButtonItem = rightButton
        
        
        self.contentView.isHidden = false
        
//        self.tripBtn.clickDelegate = self
        
        getAddressFrmLocation = GetAddressFromLocation(uv: self, addressFoundDelegate: self)
        
        btnPanGue.addTarget(self, action: #selector(self.btnPanning(sender:)))
        self.tripBtn.isUserInteractionEnabled = true
        self.tripBtn.addGestureRecognizer(btnPanGue)
        
        if(self.getPubNubConfig().uppercased() == "YES"){
            configPubNub = ConfigPubNub()
            configPubNub!.iTripId = self.tripData.get("TripId")
            configPubNub!.buildPubNub()
        }else{
            self.updateDriverLoc = UpdateDriverLocations(uv: self)
            self.updateDriverLoc.scheduleDriverLocUpdate()
        }
        
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        if(self.topNavView != nil){
            self.topNavView.navigateLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NAVIGATE")
            self.topNavView.navOptionView.backgroundColor = UIColor.UCAColor.AppThemeColor_1
            //        self.navView.subviews[0].subviews[1].subviews[0].backgroundColor = UIColor(hex: 0xFFFFFF)
            GeneralFunctions.setImgTintColor(imgView: self.topNavView.navImgView, color: UIColor.UCAColor.AppThemeTxtColor_1)
            self.topNavView.navigateLbl.textColor = UIColor.UCAColor.AppThemeTxtColor_1
            
            let navViewTapGue = UITapGestureRecognizer()
            navViewTapGue.addTarget(self, action: #selector(self.navViewTapped))
            self.topNavView.navOptionView.isUserInteractionEnabled = true
            self.topNavView.navOptionView.addGestureRecognizer(navViewTapGue)
        }
        
        
        self.observeCancelTripRequest()
        self.observeTripDestionationAdd()
        initializeMenu()
        
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
//        updateDirections = UpdateDirections(uv: self, gMap: gMapView, destinationLocation: passengerLocation, navigateView: navView)
//        updateDirections.scheduleDirectionUpdate()
        
        if(isTripStarted){
            if(tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                
                self.tripBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_SLIDE_END_DELIVERY" : "LBL_BTN_SLIDE_END_TRIP_TXT"))
            }else{
                
                self.tripBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_SLIDE_END_DELIVERY" : "LBL_BTN_SLIDE_END_TRIP_TXT"))
                
                
                if(self.tripBtn.button != nil){
                    self.tripBtn.button!.titleEdgeInsets.left = Configurations.isRTLMode() ? 40 : 80
                    self.tripBtn.button!.titleEdgeInsets.right = Configurations.isRTLMode() ? 80 : 40
                    self.tripBtn.button!.titleLabel?.lineBreakMode = .byWordWrapping
                    self.tripBtn.button!.titleLabel?.numberOfLines = 2
                    self.tripBtn.button!.titleLabel?.adjustsFontSizeToFitWidth = true
                    self.tripBtn.button!.titleLabel?.minimumScaleFactor = 0.6
                }
            }
            
            
            updateTripLocationService = UpdateTripLocationService(uv: self)
            updateTripLocationService.tripId = tripData.get("TripId")
            updateTripLocationService.scheduleDriverLocUpdate()
            self.btnIconImgView.image = UIImage(named: "ic_btn_trip_end")
        }else{
            if(tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                self.tripBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_SLIDE_BEGIN_DELIVERY" : "LBL_BTN_SLIDE_BEGIN_TRIP_TXT"))
            }else{
                self.tripBtn.setButtonTitle(buttonTitle:  self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_SLIDE_BEGIN_DELIVERY" : "LBL_BTN_SLIDE_BEGIN_TRIP_TXT"))
                
                if(self.tripBtn.button != nil){
                    self.tripBtn.button!.titleEdgeInsets.left = Configurations.isRTLMode() ? 40 : 80
                    self.tripBtn.button!.titleEdgeInsets.right = Configurations.isRTLMode() ? 80 : 40
                    self.tripBtn.button!.titleLabel?.lineBreakMode = .byWordWrapping
                    self.tripBtn.button!.titleLabel?.numberOfLines = 2
                    self.tripBtn.button!.titleLabel?.adjustsFontSizeToFitWidth = true
                    self.tripBtn.button!.titleLabel?.minimumScaleFactor = 0.6
                }
            }
            
            self.btnIconImgView.image = UIImage(named: "ic_btn_trip_start")
        }
        
        GeneralFunctions.setImgTintColor(imgView: self.btnIconImgView, color: UIColor.UCAColor.AppThemeColor)
        
        self.addDestinationLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
        
        
        
        if (tripData.get("DestLocLatitude") != "" && tripData.get("DestLocLatitude") != "0"
            && tripData.get("DestLocLongitude") != "" && tripData.get("DestLocLongitude") != "0" && (tripData.get("REQUEST_TYPE").uppercased() != Utils.cabGeneralType_UberX.uppercased() || (tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && tripData.get("eFareType") == "Regular"))) {
            
            let destLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripData!.get("DestLocLatitude")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripData!.get("DestLocLongitude")))
            
            updateDirections = UpdateDirections(uv: self, gMap: gMapView, destinationLocation: destLocation, navigateView: topNavView)
            updateDirections.scheduleDirectionUpdate()

            self.topNavView.isHidden = false
            self.addDestinationView.isHidden = true
            if(self.addDestView != nil){
                self.addDestLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
                self.addDestView.isHidden = true
            }
            addDestMarker(location: destLocation)
        }else{
            
//            self.navigateView.isHidden = true
            self.addDestinationView.isHidden = false
            
            let addDestTapGue = UITapGestureRecognizer()
            addDestTapGue.addTarget(self, action: #selector(self.addDestinationTapped))
            
            self.addDestinationView.isUserInteractionEnabled = true
            self.addDestinationView.addGestureRecognizer(addDestTapGue)
            
            if(self.addDestView != nil){
                self.addDestLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
                self.addDestView.isUserInteractionEnabled = true
                self.addDestView.addGestureRecognizer(addDestTapGue)
            }
        }
        
        if(tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && (tripData.get("DestLocLatitude") == "" || tripData.get("DestLocLongitude") == "" || tripData.get("eFareType") != "Regular")){
            
            if(self.topNavView != nil){
                self.topNavView.isHidden = true
            }
            
            if(self.addDestinationView != nil){
                self.addDestinationView.isHidden = true
            }
        }
        
       
        
        self.emeImgView.isHidden = false
        self.emeImgView.isUserInteractionEnabled = true
        let emeTapGue = UITapGestureRecognizer()
        emeTapGue.addTarget(self, action: #selector(self.emeImgViewTapped))
        self.emeImgView.addGestureRecognizer(emeTapGue)
            
        if(tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && tripData.get("eFareType") != "Regular"){
            self.emeImgView.isHidden = true
            self.googleLogoImgView.isHidden = true
            
            
            if(tripData.get("eFareType") == "Fixed"){
                self.progressViewHeight.constant = 0
                self.progressView.isHidden = true
                
                headerViewHeight = headerViewHeight - 190
            }else if(tripData.get("eFareType") != "Fixed"){
                if(isTripStarted == false){
                    self.progressViewHeight.constant = 150
////                    self.progressView.isHidden = true
                    self.progressBtn.isHidden = true
                    headerViewHeight = headerViewHeight - 40
                }
            }
            
            self.tableView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: self.btnIconImgView.bounds.height + 10, right: 0)

            setUFXHeaderViewHeight()
            
            self.tableView.delegate = self
            self.tableView.dataSource = self
            self.tableView.tableFooterView = UIView()
            self.tableView.register(UINib(nibName: "MyOnGoingTripDetailsTVCell", bundle: nil), forCellReuseIdentifier: "MyOnGoingTripDetailsTVCell")
//            self.tableView.contentInset = UIEdgeInsets(top: 8, left: 0, bottom: 8, right: 0)
            
            self.jobStatusTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROGRESS_HINT")
            self.progressStatusTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_JOB_TIMER_HINT")
            
            self.hourHLbl.text = self.generalFunc.getLanguageLabel(origValue: "HOURS", key: "LBL_HOUR_TXT").uppercased()
            self.minuteHLbl.text = self.generalFunc.getLanguageLabel(origValue: "MINUTES", key: "LBL_MINUTES_TXT").uppercased()
            self.secHLbl.text = self.generalFunc.getLanguageLabel(origValue: "SECONDS", key: "LBL_SECONDS_TXT").uppercased()
            
            self.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Resume", key: "LBL_RESUME"))
            self.progressBtn.clickDelegate = self
            
            self.hourVLbl.text = "00"
            self.minuteVLbl.text = "00"
            self.secVLbl.text = "00"
            
            Utils.createRoundedView(view: self.hourVLbl, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
            Utils.createRoundedView(view: self.minuteVLbl, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
            Utils.createRoundedView(view: self.secVLbl, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
            
            let bottomPointImg = UIImage(named: "ic_bottom_anchor_point", in: Bundle(for: ActiveTripUV.self), compatibleWith: self.traitCollection)
            
            let iv = UIImageView(image: bottomPointImg)
            
            detailBottomVIew.backgroundColor = UIColor(patternImage: UIImage(named: "ic_bottom_anchor_point")!)
            bottomPointViewHeight.constant = iv.frame.height
            
            self.contentView.isHidden = true
            self.getData()
        }
        
        checkLocationEnabled()
        addBackgroundObserver()
        
    }
    
    func mapView(_ mapView: GMSMapView, didTap marker: GMSMarker) -> Bool {
        mapView.selectedMarker = nil
        return true
    }
    func setUFXHeaderViewHeight(){
        
        self.tableView.parallaxHeader.view = self.ufxHeaderView
        self.tableView.parallaxHeader.height = headerViewHeight
        self.tableView.parallaxHeader.mode = .bottom
    }
    
    func addBackgroundObserver(){
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func checkLocationEnabled(){
        if(locationDialog != nil){
            locationDialog.removeView()
            locationDialog = nil
        }
        
        if(GeneralFunctions.hasLocationEnabled() == false || InternetConnection.isConnectedToNetwork() == false){
            locationDialog = OpenLocationEnableView(uv: self, containerView: self.contentView, gMapView: self.gMapView, isMapLocEnabled: false)
            locationDialog.show()
            return
        }
        
    }
    
    func appInForground(){
        checkLocationEnabled()
        
        if(self.configPubNub != nil){
            self.configPubNub!.unSubscribeToPrivateChannel()
            self.configPubNub!.subscribeToPrivateChannel()
        }
    }
    
    
    func emeImgViewTapped(){
        let confirmEmergencyTapUV = GeneralFunctions.instantiateViewController(pageName: "ConfirmEmergencyTapUV") as! ConfirmEmergencyTapUV
        confirmEmergencyTapUV.iTripId = tripData.get("TripId")
        self.pushToNavController(uv: confirmEmergencyTapUV)
    }
    
    func addDestinationTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
//        addDestinationUv.centerLocation = self.currentLocation
        self.pushToNavController(uv: addDestinationUv)
    }
    
    func navViewTapped(){
        let openNavOption = OpenNavOption(uv: self, containerView: self.view, placeLatitude: tripData!.get("DestLocLatitude"), placeLongitude: tripData!.get("DestLocLongitude"))
        openNavOption.chooseOption()
     
    }
    
    func releaseAllTask(isDismiss:Bool = true){
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        if(configPubNub != nil){
            configPubNub!.releasePubNub()
        }
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        
        if(updateDriverLoc != nil){
            self.updateDriverLoc.releaseTask()
            self.updateDriverLoc = nil
        }
        
        if(updateDirections != nil){
            self.updateDirections.releaseTask()
            if(self.updateDirections.gMap != nil){
                self.updateDirections.gMap!.stopRendering()
                self.updateDirections.gMap!.removeFromSuperview()
                self.updateDirections.gMap!.clear()
                self.updateDirections.gMap!.delegate = nil
                self.updateDirections.gMap = nil
            }
            self.updateDirections = nil
        }
        
        if(updateTripLocationService != nil){
            self.updateTripLocationService.releaseTask()
            self.updateTripLocationService = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
        
        if(isDismiss){
            self.dismiss(animated: false, completion: nil)
            self.navigationController?.dismiss(animated: false, completion: nil)
        }
    }
    
//    func onHeadingUpdate(heading: Double) {
//        driverMarker.rotation = heading
//    }
    func onHeadingUpdate(heading: Double) {
        //        driverMarker.isFlat = true
        //        driverMarker.rotation = heading
        //
        //        self.gMapView.animate(toBearing: heading - 20)
        currentHeading = heading
        
        if(isFirstHeadingCompleted == false){
            updateDriverMarker()
            isFirstHeadingCompleted = true
        }
    }
    
    func onLocationUpdate(location: CLLocation) {
        
        self.currentLocation = location
        
        if(tripData.get("REQUEST_TYPE").uppercased() != Utils.cabGeneralType_UberX.uppercased() || (tripData.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() && tripData.get("eFareType") == "Regular")){
            if(gMapView == nil){
                releaseAllTask()
                return
            }
            
            
            var currentZoomLevel:Float = self.gMapView.camera.zoom
            
            if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
                currentZoomLevel = Utils.defaultZoomLevel
            }
            let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                                  longitude: location.coordinate.longitude, zoom: currentZoomLevel)
            
            self.gMapView.animate(to: camera)
            
            isFirstLocationUpdate = false
            updateDriverMarker()
        }
        
        updateLocationToPubNub()
        
        
    }
    
    
    func updateDriverMarker(){
        if(currentLocation == nil || gMapView == nil){
            return
        }
        
        driverMarker.title = GeneralFunctions.getMemberd()
        
        var rotationAngle:Double = 0
        if(currentRotatedLocation == nil){
            rotationAngle = currentHeading
            
            if(currentHeading > 1){
                currentRotatedLocation = currentLocation
            }
        }else{
            rotationAngle = currentRotatedLocation.bearingToLocationDegrees(destinationLocation: currentLocation, currentRotation: driverMarker.rotation)
            if(rotationAngle == -1){
                rotationAngle = currentHeading
            }else{
                currentRotatedLocation = currentLocation
            }
        }
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            rotationAngle = 0
        }
        
//        Utils.updateMarker(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 1.0)
        
        let previousItemOfMarker = Utils.getLastLocationDataOfMarker(marker: driverMarker)
        
        var tempData = [String:String]()
        tempData["vLatitude"] = "\(currentLocation.coordinate.latitude)"
        tempData["vLongitude"] = "\(currentLocation.coordinate.longitude)"
        tempData["iDriverId"] = "\(GeneralFunctions.getMemberd())"
        tempData["RotationAngle"] = "\(rotationAngle)"
        tempData["LocTime"] = "\(Utils.currentTimeMillis())"
        
        if(previousItemOfMarker.get("LocTime") != "" && (tempData as NSDictionary).get("LocTime") != ""){
            
            let locTime = Int64(previousItemOfMarker.get("LocTime"))
            let newLocTime = Int64((tempData as NSDictionary).get("LocTime"))
            
            if(locTime != nil && newLocTime != nil){
                
                if((newLocTime! - locTime!) > 0 && Utils.driverMarkerAnimFinished == false){
                    Utils.driverMarkersPositionList.append(tempData as NSDictionary)
                }else if((newLocTime! - locTime!) > 0){
                    Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
                }
                
            }else if((locTime == nil || newLocTime == nil) && Utils.driverMarkerAnimFinished == false){
                Utils.driverMarkersPositionList.append(tempData as NSDictionary)
            }else{
                Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
            }
            
        }else if(Utils.driverMarkerAnimFinished == false){
            Utils.driverMarkersPositionList.append(tempData as NSDictionary)
        }else{
            Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
        }

        
//        driverMarker.position = self.currentLocation.coordinate
        
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            let providerView = self.getProviderMarkerView(providerImage: UIImage(named: "ic_no_pic_user")!)
            driverMarker.icon = UIImage(view: providerView)
            (providerView.subviews[1] as! UIImageView).sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                self.driverMarker.icon = UIImage(view: providerView)
            })
            driverMarker.groundAnchor = CGPoint(x: 0.5, y: 1.0)
        }else{
           // driverMarker.icon = UIImage(named: "ic_driver_car_pin")

            let eIconType = tripData.get("eIconType")
            var iconId = "ic_driver_car_pin"
            
            if(eIconType == "Bike"){
                iconId = "ic_bike"
            }else if(eIconType == "Cycle"){
                iconId = "ic_cycle"
            }else if(eIconType == "Truck"){
                iconId = "ic_truck"
            }
            
            driverMarker.icon = UIImage(named: iconId)
            driverMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        }
//        driverMarker.icon = UIImage(named: "ic_driver_car_pin")
        driverMarker.map = self.gMapView
        driverMarker.title = GeneralFunctions.getMemberd()
        driverMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        driverMarker.isFlat = true
        
        var currentZoomLevel:Float = gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation.coordinate.latitude,
                                              longitude: self.currentLocation.coordinate.longitude, zoom: currentZoomLevel)
        
        self.gMapView.animate(to: camera)
    }
    
    func updateLocationToPubNub(){
        if(currentLocation != nil){
            configPubNub?.publishMsg(channelName: GeneralFunctions.getLocationUpdateChannel(), content: GeneralFunctions.buildLocationJson(location: currentLocation!, msgType: "LocationUpdateOnTrip"))
        }
    }
    
    
    func initializeMenu(){
        
        var items = [NSDictionary]()
        
        
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased() &&  tripData.get("eFareType") != "Regular"){
            
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CALL_TXT"),"ID" : MENU_USER_CALL] as NSDictionary)
            
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MESSAGE_TXT"),"ID" : MENU_USER_MSG] as NSDictionary)
            
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "SOS", key: "LBL_EMERGENCY_SOS_TXT"),"ID" : MENU_EMERGENCY] as NSDictionary)
            
        }else if(self.tripData!.get("eHailTrip").uppercased() != "YES"){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_VIEW_DELIVERY_DETAILS" : "LBL_VIEW_PASSENGER_DETAIL"),"ID" : MENU_USER_OR_DELIVERY_DETAIL] as NSDictionary)
        }
        
        
        
        
        
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Special Instruction", key: "LBL_SPECIAL_INSTRUCTION_TXT"),"ID" : MENU_SPECIAL_INS] as NSDictionary)
        }
        
        
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY" : "LBL_CANCEL_TRIP"),"ID" : MENU_CANCEL_TRIP] as NSDictionary)
        
        
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
                //            self.selectedCellLabel.text = items[indexPath]
                
                switch indexID {
                case self.MENU_USER_CALL:
                    let number = "\(self.tripData!.get("PPhone"))"
                    UIApplication.shared.openURL(NSURL(string:"telprompt:" + number)! as URL)
                    break
                case self.MENU_USER_MSG:
                    let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
                    
                    chatUV.receiverId = self.tripData!.get("PassengerId")
                    chatUV.receiverDisplayName = self.tripData!.get("PName")
                    chatUV.assignedtripId = self.tripData!.get("TripId")
                    chatUV.pPicName = self.tripData!.get("PPicName")
                    self.pushToNavController(uv:chatUV, isDirect: true)
                    break
                case self.MENU_EMERGENCY:
                    self.emeImgViewTapped()
                    break
                case self.MENU_USER_OR_DELIVERY_DETAIL:
                    
                        let openPassengerDetail = OpenPassengerDetail(uv:self, containerView: self.contentView)
                        openPassengerDetail.tripData = self.tripData
                        openPassengerDetail.currInst = openPassengerDetail
                        openPassengerDetail.showDetail()
                    
                    break
                
                case self.MENU_SPECIAL_INS:
                    self.generalFunc.setError(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Special Instruction", key: "LBL_SPECIAL_INSTRUCTION_TXT"), content: self.tripData!.get("tUserComment") == "" ? (self.generalFunc.getLanguageLabel(origValue: "There is a No Special Instruction", key: "LBL_NO_SPECIAL_INSTRUCTION")) : self.tripData!.get("tUserComment") )
                    break
                case self.MENU_CANCEL_TRIP:
                    let openCancelTrip = OpenCancelTrip(uv:self, containerView: self.contentView)
                    openCancelTrip.tripData = self.tripData
                    openCancelTrip.currInst = openCancelTrip
                    
                    openCancelTrip.setDelegate(onTripCanceledDelegate: self)
                    openCancelTrip.cancelTrip()
                    break
                    
                default:
                    break
                }
            }
        }else{
            menu.updateItems(items)
        }
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
    
    func onTripViewClosed(openCancelTrip:OpenCancelTrip) {
        openCancelTrip.setDelegate(onTripCanceledDelegate: nil)
    }
    
    func onTripCanceled(reason: String, comment: String, openCancelTrip:OpenCancelTrip) {
        self.cancelReason = reason
        self.cancelComment = comment
        
        openCancelTrip.setDelegate(onTripCanceledDelegate: nil)
        
        isTripEndPressed = false
        
        if(isTripStarted == true){
            getAddressFrmLocation.setLocation(latitude: currentLocation!.coordinate.latitude, longitude: currentLocation!.coordinate.longitude)
            getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow:true)
            
            return
        }
        
        let parameters = ["type":"cancelTrip","iDriverId": GeneralFunctions.getMemberd(), "iUserId": tripData!.get("PassengerId"), "iTripId": tripData!.get("TripId"), "UserType": Utils.appUserType, "Reason": reason, "Comment": comment]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.releaseAllTask()
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func addDestination(latitude: String, longitude: String, address:String, eConfirmByUser:String) {
        let parameters = ["type":"addDestination","iMemberId": GeneralFunctions.getMemberd(), "Latitude": latitude, "Longitude": longitude, "Address": address, "UserType": Utils.appUserType, "TripId": tripData!.get("TripId"), "eConfirmByUser": eConfirmByUser]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.releaseAllTask()
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    if(dataDict.get("message").uppercased() == "YES"){
                        self.destinationOnTripLatitude = latitude
                        self.destinationOnTripLongitude = longitude
                        self.destinationOnTripAddress = address
                        
                        self.openSurgeConfirmDialog(dataDict: dataDict)
                        return
                    }
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func openSurgeConfirmDialog(dataDict:NSDictionary){
        
        surgePriceView = self.generalFunc.loadView(nibName: "SurgePriceView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 390 ? 375 : Application.screenSize.width - 50
        
        var defaultHeight:CGFloat = 154
        surgePriceView.frame.size = CGSize(width: width, height: defaultHeight)
        
        surgePriceView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        surgePriceBGView = UIView()
        surgePriceBGView.backgroundColor = UIColor.black
        self.surgePriceBGView.alpha = 0
        surgePriceBGView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
        surgePriceBGView.frame = self.contentView.frame
        
        surgePriceBGView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        bgViewTapGue.addTarget(self, action: #selector(self.cancelSurgeView))
        
        surgePriceBGView.addGestureRecognizer(bgViewTapGue)
        
        surgePriceView.layer.shadowOpacity = 0.5
        surgePriceView.layer.shadowOffset = CGSize(width: 0, height: 3)
        surgePriceView.layer.shadowColor = UIColor.black.cgColor
        
        surgePriceView.alpha = 0
        self.view.addSubview(surgePriceBGView)
        self.view.addSubview(surgePriceView)
        
        
        UIView.animate(withDuration: 0.5,
                       animations: {
                        self.surgePriceBGView.alpha = 0.4
                        self.surgePriceView.alpha = 1
        },  completion: { finished in
            self.surgePriceBGView.alpha = 0.4
            self.surgePriceView.alpha = 1
        })
        
        let cancelSurgeTapGue = UITapGestureRecognizer()
        cancelSurgeTapGue.addTarget(self, action: #selector(self.cancelSurgeView))
        
        surgeLaterLbl.isUserInteractionEnabled = true
        surgeLaterLbl.addGestureRecognizer(cancelSurgeTapGue)
        
        self.surgeLaterLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRY_LATER")
        
        
            self.surgePriceVLbl.text = Configurations.convertNumToAppLocal(numStr: dataDict.get("SurgePrice"))
            self.surgePriceHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str))
            self.surgeAcceptBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCEPT_SURGE"))
        
        
        let headerTxtHeight = self.surgePriceHLbl.text!.height(withConstrainedWidth: width - 20, font: UIFont(name: "Roboto-Light", size: 17)!)
        let priceTxtHeight = self.surgePriceVLbl.text!.height(withConstrainedWidth: width - 20, font: UIFont(name: "Roboto-Light", size: 26)!)
        let payAmtTxtHeight = self.surgePayAmtLbl.text!.height(withConstrainedWidth: width - 20, font: UIFont(name: "Roboto-Light", size: 16)!)
        
        self.surgePriceHLbl.fitText()
        self.surgePayAmtLbl.fitText()
        self.surgePriceVLbl.fitText()
        
        defaultHeight = defaultHeight + headerTxtHeight + priceTxtHeight + payAmtTxtHeight
        surgePriceView.frame.size = CGSize(width: width, height: defaultHeight)
        surgePriceView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        self.surgeAcceptBtn.clickDelegate = self
        
    }
    
    func cancelSurgeView(){
        if(surgePriceView != nil){
            surgePriceView.removeFromSuperview()
        }
        
        if(surgePriceBGView != nil){
            surgePriceBGView.removeFromSuperview()
        }
    }

    func addDestMarker(location:CLLocation){
    
        destinationMarker.position = location.coordinate
        
        destinationMarker.icon = UIImage(named: "ic_destination_place_image")
        destinationMarker.map = self.gMapView
        destinationMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
    }
    
    func startTrip(isFromServicePhoto:Bool){
        if(self.currentLocation == nil){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_FOUND_TXT"))
            return
        }
        
        
        let parameters = ["type":"StartTrip","iDriverId": GeneralFunctions.getMemberd(), "TripID": tripData!.get("TripId"), "iUserId": tripData!.get("PassengerId"), "UserType": Utils.appUserType,"vLatitude" : "\(currentLocation.coordinate.latitude)","vLongitude" : "\(currentLocation.coordinate.longitude)" ]

        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        
        if(self.serviceImage != nil){
            exeWebServerUrl.uploadImage(image: self.serviceImage, completionHandler: { (response) -> Void in
                
                if(response != ""){
                    let dataDict = response.getJsonDataDict()
                    
                    if(dataDict.get("Action") == "1"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                    }else if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                
                
                self.tripTaskExecuted = false
            })
        }else{
            exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
                
                if(response != ""){
                    let dataDict = response.getJsonDataDict()
                    
                    if(dataDict.get("Action") == "1"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                    }else if(dataDict.get(Utils.message_str) == "DO_RESTART"){
                        
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                
                
                self.tripTaskExecuted = false
            })
        }
        
    }
    
    func endTrip(dAddress:String, dest_lat:String, dest_lon:String, isTripCancelled:Bool, comment:String, reason:String, isFromServicePhoto:Bool, isFromAdditionalCharges:Bool, materialFee:String, miscFee:String, providerDiscount:String){
        if(self.currentLocation == nil){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_FOUND_TXT"))
            return
        }
        
        
        if(self.tripData.get("REQUEST_TYPE") == Utils.cabGeneralType_UberX && isFromAdditionalCharges == false){
            let openAdditionalCharges = OpenAdditionalChargesView(uv: self, containerView: self.contentView, tripData: self.tripData, dest_lat: dest_lat, dest_lon: dest_lon, iTripTimeId: self.iTripTimeId)
            openAdditionalCharges.setViewHandler(handler: { (isSkipped, materialFee, miscFee, providerDiscount) in
                if(isSkipped == false){
                    self.endTrip(dAddress: dAddress, dest_lat: dest_lat, dest_lon: dest_lon, isTripCancelled: isTripCancelled, comment: comment, reason: reason, isFromServicePhoto: isFromServicePhoto, isFromAdditionalCharges: true, materialFee: materialFee, miscFee: miscFee, providerDiscount: providerDiscount)
                }else{
                    self.endTrip(dAddress: dAddress, dest_lat: dest_lat, dest_lon: dest_lon, isTripCancelled: isTripCancelled, comment: comment, reason: reason, isFromServicePhoto: isFromServicePhoto, isFromAdditionalCharges: true, materialFee: "", miscFee: "", providerDiscount: "")
                }
            })
            openAdditionalCharges.show(currentFare: "")
            return
        }
        
        var latitudeList = ""
        var longitudeList = ""
        
        if(updateTripLocationService != nil){
            if(self.latitudeList.count == 0 || self.longitudeList.count == 0){
                self.latitudeList.removeAll()
                self.longitudeList.removeAll()
                
            }
            
             latitudeList = updateTripLocationService.latitudeList.joined(separator:",")
             longitudeList = updateTripLocationService.longitudeList.joined(separator:",")
        
        }else{
             latitudeList = ""
             longitudeList = ""
        }
        let parameters = ["type":"ProcessEndTrip", "TripId": tripData!.get("TripId"), "latList": "\(latitudeList)", "lonList": "\(longitudeList)", "PassengerId": tripData!.get("PassengerId"),"DriverId": GeneralFunctions.getMemberd(), "dAddress": dAddress, "dest_lat": dest_lat, "dest_lon": dest_lon, "UserType": Utils.appUserType, "isTripCanceled": isTripCancelled == true ? "true" : "", "Comment": isTripCancelled == true ? comment : "", "Reason": isTripCancelled == true ? reason : "", "fMaterialFee": materialFee, "fMiscFee": miscFee, "fDriverDiscount": providerDiscount]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        
        if(self.serviceImage != nil){
            exeWebServerUrl.uploadImage(image: self.serviceImage, completionHandler: { (response) -> Void in
                
                if(response != ""){
                    let dataDict = response.getJsonDataDict()
                    
                    if(dataDict.get("Action") == "1"){
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                    }else if(dataDict.get(Utils.message_str) == "DO_RESTART"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                self.tripTaskExecuted = false
            })
        }else{
            exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
                
                if(response != ""){
                    let dataDict = response.getJsonDataDict()
                    
                    if(dataDict.get("Action") == "1"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                    }else if(dataDict.get(Utils.message_str) == "DO_RESTART"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                    }else{
                        self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self)
                }
                self.tripTaskExecuted = false
            })
        }
        
    }
    
//    override func touchesMoved(_ touches: Set<UITouch>, with event: UIEvent?) {
//        super.touchesMoved(touches, with: event)
//        
//        let touch: UITouch = touches.first as! UITouch
//        
//        if (touch.view == self.tripBtn){
//            print("touchesMoved | This is an ImageView")
//            
//            let point = touch.location(in: self.view)
//            
//            let pointX = point.x
//            let pointY = point.y
//            
//            print("PointX:\(pointX)")
//        }else{
//            print("touchesMoved | This is not an ImageView")
//        }
//    }
    
//    var btnPanTaskComplete = false
    
    func btnPanning(sender:UIPanGestureRecognizer){
        
        if (Configurations.isRTLMode() ? sender.isLeft() : sender.isRight()) {
            let center = sender.view?.center
            let translation = sender.translation(in: sender.view)
//            center = CGPoint(center!.x + translation.x, center!.y + translation.y)
//            sender.view?.center = center!
//            sender .setTranslation(CGPoint.zero, in: sender.view)
            

            if((Configurations.isRTLMode() ? (translation.x + center!.x < 0) : (translation.x > center!.x)) && tripTaskExecuted == false){
//                btnPanTaskComplete = true
//                self.tripBtn.removeGestureRecognizer(btnPanGue)
                
                tripTaskExecuted = true
                
//                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(4 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//                    self.tripBtn.addGestureRecognizer(self.btnPanGue)
//                    self.btnPanTaskComplete = false
//                })
                
                if(self.isTripStarted){
                    
                    
                    
                    continueEndTrip()
                    
//                    self.endTrip()
                }else{
                    self.startTrip(isFromServicePhoto: false)
                }
            }
        }
    }
    
    func continueEndTrip(){
        if(currentLocation != nil){
            
            self.isTripEndPressed = true
            
            getAddressFrmLocation.setLocation(latitude: currentLocation!.coordinate.latitude, longitude: currentLocation!.coordinate.longitude)
            getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: true)
        }else{
            
            tripTaskExecuted = false
        }
    }
    
    func onAddressFound(address: String, location: CLLocation, isPickUpMode:Bool, dataResult:String) {
        if(address == ""){
            tripTaskExecuted = false
            return
        }
        if(isTripEndPressed == true){
            self.endTrip(dAddress: address, dest_lat: "\(location.coordinate.latitude)", dest_lon: "\(location.coordinate.longitude)", isTripCancelled: false, comment: "", reason: "", isFromServicePhoto: false, isFromAdditionalCharges: false, materialFee: "", miscFee: "", providerDiscount: "")
        }else{
            self.endTrip(dAddress: address, dest_lat: "\(location.coordinate.latitude)", dest_lon: "\(location.coordinate.longitude)", isTripCancelled: true, comment: self.cancelComment, reason: self.cancelReason, isFromServicePhoto: false, isFromAdditionalCharges: false, materialFee: "", miscFee: "", providerDiscount: "")
        }
    }
    
    func getData(){
        loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        loaderView.backgroundColor = UIColor.clear
        self.ufxCntView.isHidden = true
        let parameters = ["type":"getTripDeliveryLocations", "iTripId": tripData.get("TripId"), "userType": Utils.appUserType,"iUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let driverDetails = dataDict.getObj(Utils.message_str).getObj("driverDetails")
                    Utils.createRoundedView(view: self.senderImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 1)
                    
                    self.senderImgView.sd_setImage(with: URL(string: CommonUtils.passenger_image_url + "\(driverDetails.get("iUserId"))/\(driverDetails.get("riderImage"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                        
                    })
                    
                    self.senderNameLbl.textColor = UIColor.UCAColor.AppThemeColor
                    self.senderNameLbl.text = driverDetails.get("riderName")
                    self.ratingView.rating = GeneralFunctions.parseFloat(origValue: 0, data: driverDetails.get("riderRating"))
                    self.sourceAddLbl.text = driverDetails.get("tSaddress").trim()
                    self.sourceAddLbl.fitText()
                    
                    let extraHeight = self.sourceAddLbl.text!.height(withConstrainedWidth: Application.screenSize.width - 106, font: UIFont(name: "Roboto-Light", size: 14)!) - 17

                    self.senderViewHeight.constant = 110 + extraHeight
                    self.headerViewHeight = self.headerViewHeight + extraHeight
                    self.setUFXHeaderViewHeight()
                    
                    let dataArr = dataDict.getObj(Utils.message_str).getArrObj("States")
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                    }
                    
                    self.tableView.reloadData()
                    
                    self.iTripTimeId = self.tripData.get("iTripTimeId")
                    
                    let totalSecond = GeneralFunctions.parseDouble(origValue: 0.0, data: self.tripData.get("TotalSeconds"))
                    self.totalSecond = totalSecond
                    
                    let hours = Int(totalSecond / 3600)
                    let minutes = Int(totalSecond.truncatingRemainder(dividingBy: 3600) / 60)
                    let seconds = Int(totalSecond.truncatingRemainder(dividingBy: 3600).truncatingRemainder(dividingBy: 60))
                    
                    self.hourVLbl.text = String(format: "%02d", hours)
                    self.minuteVLbl.text = String(format: "%02d", minutes)
                    self.secVLbl.text = String(format: "%02d", seconds)
                    
                    if(self.tripData.get("TimeState") == "Resume"){
                        self.isResume = true
                        self.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Pause", key: "LBL_PAUSE"))
                        self.runJobTimer()
                    }else{
                        self.isResume = false
                        self.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Resume", key: "LBL_RESUME"))
                    }
                    
                    if self.isResume{
                        self.isResume = false
                    }else{
                        self.isResume = true
                    }
                    
                }else{
//                    _ = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
                self.contentView.isHidden = false
                
                self.ufxCntView.isHidden = false
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
    }
    
    func runJobTimer(){
        if(jobTimer != nil){
            jobTimer!.invalidate()
        }
        
        jobTimer =  Timer.scheduledTimer(timeInterval: 1, target: self, selector: #selector(self.updateJobTimerValue), userInfo: nil, repeats: true)
        
        jobTimer.fire()
    }
    
    func stopJobTimer(){
        if(jobTimer != nil){
            jobTimer!.invalidate()
        }
    }
    
    func updateJobTimerValue(){
        
        self.totalSecond = self.totalSecond + 1
        
        let hours = Int(totalSecond / 3600)
        let minutes = Int(totalSecond.truncatingRemainder(dividingBy: 3600) / 60)
        let seconds = Int(totalSecond.truncatingRemainder(dividingBy: 3600).truncatingRemainder(dividingBy: 60))
        
        self.hourVLbl.text = String(format: "%02d", hours)
        self.minuteVLbl.text = String(format: "%02d", minutes)
        self.secVLbl.text = String(format: "%02d", seconds)
    }
    
    func setJobTimeStatus(){
        var parameters = ["type":"SetTimeForTrips", "iTripId": tripData!.get("TripId"), "UserType": Utils.appUserType]
        
        if(!self.isResume){
            parameters["iTripTimeId"] = self.iTripTimeId
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    
                    let totalSecond = GeneralFunctions.parseDouble(origValue: 0.0, data: dataDict.get("totalTime"))
                    self.totalSecond = totalSecond
                    
                    let hours = Int(totalSecond / 3600)
                    let minutes = Int(totalSecond.truncatingRemainder(dividingBy: 3600) / 60)
                    let seconds = Int(totalSecond.truncatingRemainder(dividingBy: 3600).truncatingRemainder(dividingBy: 60))
                    
                    self.hourVLbl.text = String(format: "%02d", hours)
                    self.minuteVLbl.text = String(format: "%02d", minutes)
                    self.secVLbl.text = String(format: "%02d", seconds)
                    
                    if(!self.isResume){
                        self.stopJobTimer()
                        
                        self.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Resume", key: "LBL_RESUME"))
                    }else{
                        
                        self.iTripTimeId = dataDict.get(Utils.message_str)
                        
                        self.progressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Pause", key: "LBL_PAUSE"))
                        
                        
                        self.runJobTimer()
                    }
                   
                    
                    if self.isResume{
                        self.isResume = false
                    }else{
                        self.isResume = true
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            self.tripTaskExecuted = false
        })
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.dataArrList.count
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
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
    func myBtnTapped(sender: MyButton) {
        if(self.progressBtn != nil && sender == self.progressBtn){
            self.setJobTimeStatus()
        }else if(surgeAcceptBtn != nil && sender == surgeAcceptBtn){
            self.cancelSurgeView()
           
            self.addDestination(latitude: self.destinationOnTripLatitude, longitude: self.destinationOnTripLongitude, address: self.destinationOnTripAddress, eConfirmByUser: "Yes")
        }
    }
    
    @IBAction func unwindToActiveTrip(_ segue:UIStoryboardSegue) {
        
        if(segue.source.isKind(of: AddDestinationUV.self)){
            
            let addDestUv = segue.source as! AddDestinationUV
            
            addDestination(latitude: "\(addDestUv.selectedLocation.coordinate.latitude)", longitude: "\(addDestUv.selectedLocation.coordinate.longitude)", address: "\(addDestUv.selectedAddress)", eConfirmByUser: "No")
        }
        
    }
}
