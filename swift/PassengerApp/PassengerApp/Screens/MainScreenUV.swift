//
//  MainScreenUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 11/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps

class MainScreenUV: UIViewController, GMSMapViewDelegate, OnLocationUpdateDelegate, AddressFoundDelegate, UICollectionViewDelegate, UICollectionViewDataSource , MyBtnClickDelegate, OnDirectionUpdateDelegate, OnTaskRunCalledDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
    @IBOutlet weak var myLocImgView: UIImageView!
    @IBOutlet weak var myLocBottomMargin: NSLayoutConstraint!
    @IBOutlet weak var setPrefLocImgView: UIImageView!
    @IBOutlet weak var menuImgView: UIImageView!
    //    @IBOutlet weak var topRightView: UIView!
    //    @IBOutlet weak var bottomLeftView: UIView!
    @IBOutlet weak var sourcePinImgView: UIImageView!
    @IBOutlet weak var sourcePinImgViewWidth: NSLayoutConstraint!
    @IBOutlet weak var sourcePinImgViewHeight: NSLayoutConstraint!
    @IBOutlet weak var sourcePinImgViewOffset: NSLayoutConstraint!
    @IBOutlet weak var sourcePickUpEtaLbl: MyLabel!
    @IBOutlet weak var setPrefImgViewBottomMargin: NSLayoutConstraint!
    //    @IBOutlet weak var myLocImgViewBottomMargin: NSLayoutConstraint!
    
    
    
    //GenderView related OutLets
    @IBOutlet weak var genderVCloseImgView: UIImageView!
    @IBOutlet weak var genderHLbl: MyLabel!
    @IBOutlet weak var maleImgView: UIImageView!
    @IBOutlet weak var femaleImgView: UIImageView!
    @IBOutlet weak var maleLbl: MyLabel!
    @IBOutlet weak var femaleLbl: MyLabel!
    
    
    //Request PickUp BottomView OutLets
    @IBOutlet weak var requestPickUpLoaderContainerView: UIView!
    @IBOutlet weak var cabTypeContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var cabTypeCollectionView: UICollectionView!
    @IBOutlet weak var noCabTypeLbl: MyLabel!
    @IBOutlet weak var cashPaymentView: UIView!
    @IBOutlet weak var cardPaymentView: UIView!
    @IBOutlet weak var cashPayLbl: MyLabel!
    @IBOutlet weak var cardPayLbl: MyLabel!
    @IBOutlet weak var paymentContainerView: UIView!
    @IBOutlet weak var promoCodeLbl: MyLabel!
    @IBOutlet weak var promoCodeView: UIView!
    @IBOutlet weak var requestNowBtn: MyButton!
    @IBOutlet weak var rideLaterBtn: MyButton!
    @IBOutlet weak var rideLaterImgView: UIImageView!
    @IBOutlet weak var rideLaterImgViewLeading: NSLayoutConstraint!
    @IBOutlet weak var rideLaterImgViewWidth: NSLayoutConstraint!
    @IBOutlet weak var paymentOptionContainerView: UIView!
    @IBOutlet weak var paymentOptionContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var seperaterViewXPostion: NSLayoutConstraint!
    @IBOutlet weak var payViewHeight: NSLayoutConstraint!
    @IBOutlet weak var payView: UIView!
    @IBOutlet weak var payImgView: UIImageView!
    @IBOutlet weak var payLbl: MyLabel!
    
    
    //Confirm Card Outlets
    @IBOutlet weak var confirmCardHLbl: MyLabel!
    @IBOutlet weak var confirmCardVLbl: MyLabel!
    @IBOutlet weak var confirmCardLbl: MyLabel!
    @IBOutlet weak var changeCardLbl: MyLabel!
    @IBOutlet weak var cancelCardLbl: MyLabel!
    
    // Promocode Outlets
    @IBOutlet weak var promoViewHLbl: MyLabel!
    @IBOutlet weak var promoTxtField: MyTextField!
    @IBOutlet weak var confirmPromoLbl: MyLabel!
    @IBOutlet weak var cancelPromoLbl: MyLabel!
    
    // RequestCab OutLets
    @IBOutlet weak var rippleBgView: UIView!
    @IBOutlet weak var cancelCabReqImgView: UIImageView!
    @IBOutlet weak var cancelCabReqBgView: UIView!
    @IBOutlet weak var reqNoteLbl: MyLabel!
    @IBOutlet weak var retryReqBtn: MyButton!
    @IBOutlet weak var requestCabTopView: UIView!
    //    @IBOutlet weak var requestCabNavBar: CustomNavBar!
    //    @IBOutlet weak var requestCanNavBarTitleView: UINavigationItem!
    @IBOutlet weak var requestCabTitleLbl: MyLabel!
    @IBOutlet weak var requestCabHeaderBarHeight: NSLayoutConstraint!
    
    // DriverDetail View OutLets
    @IBOutlet weak var deliveryConfirmCodeLbl: MyLabel!
    @IBOutlet weak var deliveryConfirmCodeLblHeight: NSLayoutConstraint!
    @IBOutlet weak var driverPicImgView: UIImageView!
    @IBOutlet weak var driverNameLbl: MyLabel!
    @IBOutlet weak var driverRatingBar: RatingView!
    @IBOutlet weak var driverRatingLbl: MyLabel!
    @IBOutlet weak var driverCardNameLbl: MyLabel!
    @IBOutlet weak var driverCarType: MyLabel!
    @IBOutlet weak var carIcImgView: UIImageView!
    @IBOutlet weak var carIcBgView: UIView!
    @IBOutlet weak var driverCallImgView: UIImageView!
    @IBOutlet weak var driverCarPlateNoLbl: MyLabel!
    @IBOutlet weak var driverCallView: UIView!
    @IBOutlet weak var driverCallLbl: MyLabel!
    @IBOutlet weak var driverMsgImgView: UIImageView!
    @IBOutlet weak var driverMsgView: UIView!
    @IBOutlet weak var driverMsgLbl: MyLabel!
    @IBOutlet weak var driverCancelTripImgView: UIImageView!
    @IBOutlet weak var driverCancelTripView: UIView!
    @IBOutlet weak var driverCancelTripLbl: MyLabel!
    @IBOutlet weak var driverShareImgView: UIImageView!
    @IBOutlet weak var driverShareView: UIView!
    @IBOutlet weak var driverShareLbl: MyLabel!
    
    // Surge Price OutLets
    @IBOutlet weak var surgePriceHLbl: MyLabel!
    @IBOutlet weak var surgePriceVLbl: MyLabel!
    @IBOutlet weak var surgePayAmtLbl: MyLabel!
    @IBOutlet weak var surgeAcceptBtn: MyButton!
    @IBOutlet weak var surgeLaterLbl: MyLabel!
    
    var genderView:UIView!
    
    var navItem:UINavigationItem!
    
    var userProfileJson:NSDictionary!
    
    var requestNowBtnEnabled = true
    
    var getLocation:GetLocation!
    
    var gMapView:GMSMapView!
    
    var currentLocation:CLLocation?
    var pickUpLocation:CLLocation!
    var pickUpAddress = ""
    var destLocation:CLLocation!
    var destAddress = ""
    
    var vVehicleImgPath = CommonUtils.webServer + "webimages/icons/VehicleType/"
    var vVehicleDefaultImgPath = CommonUtils.webServer + "webimages/icons/DefaultImg/"
    
    var configPubNub:ConfigPubNub?
    var window:UIWindow!
    var isDataSet = false
    
    let generalFunc = GeneralFunctions()
    
    var isFirstLocationUpdate = true
    
    
    var ufxCurrentLoadedDriverList = [NSDictionary]()
    var currentLoadedDriverList = [NSDictionary]()
    var currentLoadedDriverIdsArr = [String]()
    
    var selectedCabTypeId = ""
    
    var loadAvailableCab:LoadAvailableCab!
    
    var addressContainerView:AddressContainerView!
    var requestPickUpView:UIView!
    
    
    var pickUpAddViewFrame:CGRect!
    var destAddViewFrame:CGRect!
    
    var getAddressFrmLocation:GetAddressFromLocation!
    
    var isPickUpMode = true
    var tempIsPickUpMode = false

    var cabTypesArr = [NSDictionary]()
    var cabTypesFareArr = [NSDictionary]()
    
    var isCashPayment = true
    
    var isCardValidated = false
    
    var promoCodeDialogView:UIView!
    var promoCodeBGDialogView:UIView!
    
    var surgePriceView:UIView!
    var surgePriceBGView:UIView!
    
    var confirmCardDialogView:UIView!
    var confirmCardBGDialogView:UIView!
    
    var requestCabView:UIView!
    var driverDetailView:UIView!
    
    var appliedPromoCode = ""
    var specialInstruction = ""
    
    var isDriverAssigned = false
    var isTripStarted = false
    var isTripFinished = false
    var assignedDriverId = ""
    var assignedTripId = ""
    var eTripType = ""
    
    var waitingMarker:GMSMarker!
    
    var assignedDriverData = [String:String]()
    
    var selectedDate = ""
    var selectedTimeZone = ""
    
    var isRideLaterFromSurge = false
    
    var isDriverArrived = false
    
    var assignedDriverLocation:CLLocation!
    
    var assignedDriverRotatedLocation:CLLocation!
    
    var assignedDriverMarker:GMSMarker!
    
    var updateDirection:UpdateDirections!
    
    var updateFreqDriverLocTask:UpdateFreqTask!
    
    var driverRequestQueueTimer:Timer!
    
    var currDriverReqPosition = 0
    
    var titleLbl:MyLabel!
    
    var emeImgView:UIImageView!
    
    var reqSentErrorDialog:MTDialog!
    var isRequestExecuting = false
    
    //    var transitionBottomLeft: JTMaterialTransition?
    //
    //    var transitionTopRight: JTMaterialTransition?
    var currentTransition:JTMaterialTransition?
    
    //    var currentPickUpCountry = ""
    //    var currentPickUpCity = ""
    
    var cntView:UIView!
    
    //    var pickUpAddGeoCodeResult = ""
    //    var destAddGeoCodeResult = ""
    
    var tempPickUpAdd = ""
    var tempDestAdd = ""
    //    var tempPickUpGeoCodeResult = ""
    //    var tempDestGeoCodeResult = ""
    
    var locationDialog:OpenEnableLocationView!
    
    var currentCabGeneralType = Utils.cabGeneralType_Ride
    
    var isDeliveryDataEntered = false
    
    var receiverName = ""
    var receiverMobile = ""
    var pickUpIns = ""
    var deliveryIns = ""
    var packageTypeId = ""
    var packageDetails = ""
    
    var isRideLater_payBox = false
    var isAutoContinue_payBox = false
    
    var isDirectOpenFromUFXAddress = false
    var ufxSelectedVehicleTypeId = ""
    var ufxSelectedVehicleTypeName = ""
    
    var UFX_PAGE_MODE = ""
    
    var ufxLoaderView:UIView!
    
    var listLbl:MyLabel!
    var mapLbl:MyLabel!
    
    
    
    var ufxServiceItemDict:NSDictionary!
    var ufxSelectedLatitude = ""
    var ufxSelectedLongitude = ""
    var ufxCabBookingId = ""
    
    var ufxSelectedQty = ""
    var ufxAddressId = ""
    
    var isPreferFemaleDriverEnable = false
    var isHandicapPrefEnabled = false
    
    var isMyLocationEnabled = true
    
    
    
    var ufxSelectedServiceProviderId = ""
    
    var providerDetailMarkerView:ProviderDetailMarkerView!
    
    var ufxDriverAcceptedReqNow = false
    
    var RIDER_REQUEST_ACCEPT_TIME = 30
    
    
    var recentLocView:RecentLocationView!
    
    var arrivingNotificationCounter1 = false
    var arrivingNotificationCounter2 = false
    var arrivingNotificationCounter3 = false
    
    var lastArrivingNotificationTime:Int64!
    var isFirstAddress = true
    var isSkipAddressFind = false
    
    var loaderGeoCode:NBMaterialLoadingDialog!
    
    var isMapMoveToDriverLoc = false
    
    var fareDetailView:FareDetailView!
    var fareDetailBGView:UIView!
    
    var isSafeAreaSet = false
    var drvierDetailViewHeight:CGFloat = 220
    //    var requestCabHeaderViewHeightOffset:CGFloat = 0
    
    /* To animate Line */
    var animTask:UpdateFreqTask!
    var animationPathOrig = GMSMutablePath()
    var animationPath = GMSMutablePath()
    var i_anim:Int = 0
    
    var animationPolylineOrig = GMSPolyline()
    var animationPolyline = GMSPolyline()
    
    var animLocationArr = [CLLocationCoordinate2D]()
    var animLocArrParts:[[CLLocationCoordinate2D]]!
    
    var pickUpCustomMarker:GMSMarker!
    var dropOffCustomMarker:GMSMarker!
    var pickUpPinMarker:GMSMarker!
    var dropOffPinMarker:GMSMarker!
    var allowAnimation = true
    var oldPickUpAnchorPoint = CGPoint(-0.05, -0.05)
    /* To animate Line */
    
    var destPointMarker:GMSMarker!
    var pickUpPointMarker:GMSMarker!
    
    var addressBarYPosition:CGFloat = 0
    var addressBarHeight:CGFloat = 0
    var heightOfWaitingMarker:CGFloat = 80
    
    var isSurgeFromAddDestination = false
    var destinationOnTripLatitude = ""
    var destinationOnTripLongitude = ""
    var destinationOnTripAddress = ""
    
    
    
    var HANDICAP_ACCESSIBILITY_OPTION = ""
    var FEMALE_RIDE_REQ_ENABLE = ""
    
    var directionFailedDialog:MTDialog!
    var isRouteDrawnFailed = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        if(userProfileJson.get("APP_TYPE").uppercased() != "UBERX"){
            self.navigationController?.navigationBar.isTranslucent = true
        }
        
        //        UIApplication.shared.isStatusBarHidden = true
        
        if(recentLocView != nil){
            recentLocView.checkPlaces()
        }
        
        self.view.layoutIfNeeded()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        if(self.loadAvailableCab != nil){
            self.loadAvailableCab.onPauseCalled()
        }
        
        if(userProfileJson.get("APP_TYPE").uppercased() != "UBERX"){
            self.navigationController?.navigationBar.isTranslucent = false
        }
        
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        
        //        if(self.recentLocView != nil && self.isDriverAssigned == false && self.requestPickUpView == nil){
        //
        //        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        //        let leftButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_menu_all")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openMenu))
        //        self.navigationItem.leftBarButtonItem = leftButton
        
        cntView = self.generalFunc.loadView(nibName: "MainScreenDesign", uv: self, contentView: contentView) //, isStatusBarAvail: true
        //        cntView.frame.size = CGSize(width: cntView.frame.width, height: cntView.frame.height - 100)
        //        cntView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: Application.screenSize.height)
        
        self.contentView.addSubview(cntView)
        Utils.driverMarkersPositionList.removeAll()
        Utils.driverMarkerAnimFinished = true
        
        window = Application.window!
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInBackground), name: NSNotification.Name(rawValue: Utils.appBGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        if(self.userProfileJson.get("APP_TYPE").uppercased() == "DELIVERY"){
            self.currentCabGeneralType = Utils.cabGeneralType_Deliver
        }else if(self.userProfileJson.get("APP_TYPE").uppercased() == "UBERX"){
            self.currentCabGeneralType = Utils.cabGeneralType_UberX
        }
        
    
            setNavBar(isHidden: true)
            
        
        
        self.menuImgView.isHidden = true
        
        //        transitionBottomLeft = JTMaterialTransition(animatedView: self.bottomLeftView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35))
        //        transitionTopRight = JTMaterialTransition(animatedView: self.topRightView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35))
        
        
        
        GeneralFunctions.removeValue(key: "isDriverAssigned")
        
        
        RIDER_REQUEST_ACCEPT_TIME = GeneralFunctions.getValue(key: Utils.RIDER_REQUEST_ACCEPT_TIME_KEY) == nil ? 30 : GeneralFunctions.parseInt(origValue: 30, data: (GeneralFunctions.getValue(key: Utils.RIDER_REQUEST_ACCEPT_TIME_KEY) as! String))
        
        deleteTripStatusMessages()
    }
    
    override func closeCurrentScreen() {
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased() && requestCabView != nil){
            return
        }
        if(self.requestPickUpView != nil && self.gMapView != nil && self.loadAvailableCab != nil){
            removeAddReqPickUpView()
            
            ufxSelectedServiceProviderId = ""
            let ufxPickLoc = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLongitude))
            self.pickUpLocation = ufxPickLoc
            
            let camera = GMSCameraPosition.camera(withLatitude: self.pickUpLocation!.coordinate.latitude,
                                                  longitude: self.pickUpLocation!.coordinate.longitude, zoom: self.gMapView.camera.zoom)
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            
            getAddressFrmLocation.setLocation(latitude: pickUpLocation!.coordinate.latitude, longitude: pickUpLocation!.coordinate.longitude)
            getAddressFrmLocation.setPickUpMode(isPickUpMode: isPickUpMode)
            getAddressFrmLocation.executeProcess(isOpenLoader: false, isAlertShow:false)
            
            self.loadAvailableCab.filterDrivers(isCheckAgain: false)
            return
        }
        
   
        
        self.releaseAllTask()
        GeneralFunctions.postNotificationSignal(key: ConfigPubNub.resumeInst_key, obj: self)
        super.closeCurrentScreen()
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
    
    
    func checkLocationEnabled(){
        if(locationDialog != nil){
            locationDialog.closeView()
            locationDialog = nil
        }
        
        
        if((GeneralFunctions.hasLocationEnabled() == false && self.currentLocation == nil) || InternetConnection.isConnectedToNetwork() == false)
        {
            
            locationDialog = OpenEnableLocationView(uv: self, containerView: self.view, menuImgView: self.menuImgView)
            locationDialog.currentInst = locationDialog
            locationDialog.setViewHandler(handler: { (latitude, longitude, address, isMenuOpen) in
                //                self.currentLocation = CLLocation(latitude: latitude, longitude: longitude)
                //                self.setTripLocation(selectedAddress: address, selectedLocation: CLLocation(latitude: latitude, longitude: longitude))
                
                if(isMenuOpen){
                    if(InternetConnection.isConnectedToNetwork() == false){
                        self.continueOpenMenu()
                    }else{
                        self.openMenu()
                    }
                    
                }else{
                    self.locationDialog.closeView()
                    self.locationDialog = nil
                    self.onLocationUpdate(location: CLLocation(latitude: latitude, longitude: longitude))
                }
            })
            locationDialog.show()
            
            return
        }else{
            if(self.currentLocation != nil && self.gMapView.isMyLocationEnabled != self.isMyLocationEnabled){
                self.gMapView.isMyLocationEnabled = self.isMyLocationEnabled
            }
        }
    }
    
    deinit {
        releaseAllTask()
    }
    
    func appInBackground(){
        if(self.loadAvailableCab != nil){
            self.loadAvailableCab.onPauseCalled()
        }
        
        if(self.isDriverAssigned == true){
            self.unSubscribeToDriverLocChannel()
        }
        
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
    }
    
    func appInForground(){
        if(self.loadAvailableCab != nil && requestCabView == nil && isDriverAssigned == false){
            self.loadAvailableCab.onResumeCalled()
        }
        
        if(self.isDriverAssigned == true){
            self.subscribeToDriverLocChannel()
        }
        
        //        if(self.driverDetailView != nil){
        //            driverDetailView.frame = CGRect(x: 0, y: self.contentView.frame.height - 220, width: Application.screenSize.width, height: 220)
        //        }
        //        if(self.addressContainerView != nil){
        //            addressContainerView.frame = CGRect(x: self.contentView.bounds.midX - ((Application.screenSize.width - 60) / 2), y: 60, width: Application.screenSize.width - 60, height: 100)
        //        }
        
        if(userProfileJson != nil && userProfileJson.get("APP_TYPE").uppercased() != "UBERX"){
            checkLocationEnabled()
        }
        
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
        UIControl().sendAction(Selector(("_performMemoryWarning")), to: UIApplication.shared, for: nil)
    }
    
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            if(userProfileJson.get("APP_TYPE").uppercased() == "UBERX"){
                cntView.frame.size.height = cntView.frame.size.height - GeneralFunctions.getSafeAreaInsets().top + 10
            }
            cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            isSafeAreaSet = true
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)
        
            if(self.isDataSet == false){
                
                let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: 0.0)
                
                self.gMapView = GMSMapView.map(withFrame: self.cntView.frame, camera: camera)
                
                if(GeneralFunctions.hasLocationEnabled() == true){
                    self.gMapView.isMyLocationEnabled = self.isMyLocationEnabled
                }
                
                self.googleMapContainerView.addSubview(self.gMapView)
                
                self.gMapView.settings.allowScrollGesturesDuringRotateOrZoom = false
                
                
                self.setData()
                
                let vTripStatus = self.userProfileJson.get("vTripStatus")
                
                if(self.userProfileJson.get("APP_TYPE").uppercased() != "UBERX" && vTripStatus != "Active" && vTripStatus != "On Going Trip"){
                    self.checkLocationEnabled()
                }
                
                if(vTripStatus == "Active" || vTripStatus == "On Going Trip"){
                    let tripDetailJson = self.userProfileJson.getObj("TripDetails")
                    let pickUpLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripDetailJson.get("tStartLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripDetailJson.get("tStartLong")))
                    
                    self.onLocationUpdate(location: pickUpLocation)
                }
                
                initializeView()
                self.isDataSet = true
                
            }
            
            if(self.loadAvailableCab != nil && self.requestCabView == nil && self.isDriverAssigned == false){
                self.loadAvailableCab.onResumeCalled()
            }
        
    }
    
    
    func mapView(_ mapView: GMSMapView, willMove gesture: Bool) {
        
        if(sourcePinImgView.isHidden == true){
            self.sourcePickUpEtaLbl.isHidden = true
            return
        }
        
        if(isSkipAddressFind == false){
            if(isPickUpMode){
                self.addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                
                self.tempPickUpAdd = self.pickUpAddress
                //                self.tempPickUpGeoCodeResult = self.pickUpAddGeoCodeResult
                
                if(self.loadAvailableCab != nil){
                    self.loadAvailableCab.pickUpAddress = ""
                }
                
                //                self.pickUpAddGeoCodeResult = ""
            }else{
                self.tempDestAdd = self.destAddress
                //                self.tempDestGeoCodeResult = self.destAddGeoCodeResult
                
                self.addressContainerView.destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                //                self.destAddGeoCodeResult = ""
            }
        }
    }
    
    func mapView(_ mapView: GMSMapView, didTap marker: GMSMarker) -> Bool {
        if(requestPickUpView != nil && pickUpCustomMarker != nil && marker == pickUpCustomMarker && self.addressContainerView != nil){
            mapView.selectedMarker = nil
            self.tempIsPickUpMode = self.isPickUpMode
            self.isPickUpMode = true
            self.addressContainerView.isPickUpMode = true
            self.addressContainerView.pickUpViewTappedOnAction()
            return true
        }
        
        if(requestPickUpView != nil && dropOffCustomMarker != nil && marker == dropOffCustomMarker && self.addressContainerView != nil){
            mapView.selectedMarker = nil
            self.tempIsPickUpMode = self.isPickUpMode
            self.isPickUpMode = false
            self.addressContainerView.isPickUpMode = false
            self.addressContainerView.destViewTappedOnAction()
            return true
        }
        
        if(self.currentCabGeneralType == Utils.cabGeneralType_UberX && isDriverAssigned == false && requestPickUpView == nil){
            
            if(providerDetailMarkerView != nil){
                providerDetailMarkerView.closeViewTapped()
            }
            
            
            let driver_id = marker.title!.replace("DriverId", withString: "")
            
            for i in 0..<currentLoadedDriverList.count{
                
                let driverId = currentLoadedDriverList[i].get("driver_id")
                
                if(driverId == driver_id){
                    
                    let providerDetailMarkerView = ProviderDetailMarkerView(frame: CGRect(x: 0, y: self.view.frame.height + 160, width: Application.screenSize.width, height: 160))
                    self.providerDetailMarkerView = providerDetailMarkerView
                    providerDetailMarkerView.setViewHandler(handler: { (view, isViewClose, isMoreInfoBtnTapped) in
                        if(isViewClose){
                            //                            self.myLocImgView.frame.origin.y = self.view.frame.size.height - 15 - self.myLocImgView.frame.height
                            self.myLocBottomMargin.constant = 15
                            providerDetailMarkerView.frame.origin.y = Application.screenSize.height + 2500
                            providerDetailMarkerView.removeFromSuperview()
                        }else if(isMoreInfoBtnTapped == true){
                            //                            self.myLocImgView.frame.origin.y = self.view.frame.size.height - 15 - self.myLocImgView.frame.height
                            self.myLocBottomMargin.constant = 15
                            providerDetailMarkerView.frame.origin.y = Application.screenSize.height + 2500
                            providerDetailMarkerView.removeFromSuperview()
                        }
                    })
                    
                    let vName = currentLoadedDriverList[i].get("Name") + " " + currentLoadedDriverList[i].get("LastName")
                    
                    providerDetailMarkerView.providerNameLbl.text = vName
                    
                    providerDetailMarkerView.providerImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(currentLoadedDriverList[i].get("driver_id"))/\(currentLoadedDriverList[i].get("driver_img"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                        
                    })
                    Utils.createRoundedView(view: providerDetailMarkerView.providerImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 1)
                    
                    providerDetailMarkerView.ratingBar.rating = GeneralFunctions.parseFloat(origValue: 0, data: currentLoadedDriverList[i].get("average_rating"))
                    
                    if(currentLoadedDriverList[i].get("fAmount") != ""){
                        providerDetailMarkerView.priceLbl.text = currentLoadedDriverList[i].get("fAmount")
                        providerDetailMarkerView.priceLbl.isHidden = false
                    }else{
                        providerDetailMarkerView.priceLbl.isHidden = true
                    }
                    
                    if(self.userProfileJson.get("eUnit") == "KMs"){
                        providerDetailMarkerView.distanceLbl.text = "\(Configurations.convertNumToAppLocal(numStr: String(format: "%.02f", (GeneralFunctions.parseDouble(origValue: 0, data: currentLoadedDriverList[i].get("DIST_TO_PICKUP")).roundTo(places: 2)))))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_KM_DISTANCE_TXT")) \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_AWAY"))"
                    }else{
                        providerDetailMarkerView.distanceLbl.text = "\(String(format: "%.02f", ((GeneralFunctions.parseDouble(origValue: 0, data: currentLoadedDriverList[i].get("DIST_TO_PICKUP")) * 0.621371).roundTo(places: 2))))" + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MILE_DISTANCE_TXT")) " + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_AWAY")
                    }
                    providerDetailMarkerView.moreInfoBtn.btnType = "UFX_DRIVER_MORE_INFO"
                    providerDetailMarkerView.moreInfoBtn.tag = i
                    providerDetailMarkerView.moreInfoBtn.clickDelegate = self
                    
                    self.view.addSubview(providerDetailMarkerView)
                    
                    let myLocBottomFinalMargin = self.myLocBottomMargin.constant + 160
                    UIView.animate(withDuration: 0.5,
                                   animations: {
                                    providerDetailMarkerView.frame.origin.y = self.view.frame.size.height - 160
                                    self.myLocBottomMargin.constant = myLocBottomFinalMargin
                                    self.view.layoutIfNeeded()
                    },  completion: { finished in
                        providerDetailMarkerView.frame.origin.y = self.view.frame.size.height - 160
                        self.myLocBottomMargin.constant = myLocBottomFinalMargin
                        self.view.layoutIfNeeded()
                    })
                }
                
                
            }
            
            return true
        }
        mapView.selectedMarker = nil
        return true
    }
    
    func mapView(_ mapView: GMSMapView, idleAt position: GMSCameraPosition) {
        if(self.isDriverAssigned == true){
            return
        }
        
        if(sourcePinImgView.isHidden == true){
            self.sourcePickUpEtaLbl.isHidden = true
            return
        }
        if(isSkipAddressFind == false){
            changeAddress()
        }else{
            isSkipAddressFind = false
            
        }
    }
    
    func mapView(_ mapView: GMSMapView, didChange position: GMSCameraPosition) {
        
        if(self.requestPickUpView != nil && pickUpCustomMarker != nil && dropOffCustomMarker != nil){
            
            
            let coord = self.pickUpCustomMarker.position
            let point:CGPoint = self.gMapView.projection.point(for: coord)
            
            let bounds = UIScreen.main.bounds
            
            //CGPoint(-0.05, 1.05)// Top-Right ,CGPoint(1.05, -0.05)// Bottom-Left ,//CGPoint(1.05, 1.05) Top-Left ,//CGPoint(-0.05, -0.05) Bootom-Right
            CATransaction.begin()
            CATransaction.setAnimationDuration(0.5)
            if(point.x >= bounds.size.width - 110){
                
                self.allowAnimation = false
                if(point.y > (bounds.size.height - self.requestPickUpView.frame.size.height) / 2){
                    self.pickUpCustomMarker.groundAnchor = CGPoint(1.05, 1.05) // Top-Left
                }else{
                    self.pickUpCustomMarker.groundAnchor = CGPoint(1.05, -0.05) // Bottom-Left
                }
                
            }else if(point.x <= 110){
                
                self.allowAnimation = false
                if point.y > (bounds.size.height - self.requestPickUpView.frame.size.height) / 2{
                    self.pickUpCustomMarker.groundAnchor = CGPoint(-0.05, 1.05) // Top-Right
                }else{
                    self.pickUpCustomMarker.groundAnchor = CGPoint(-0.05, -0.05) //Bootom-Right
                }
                
            }else if(point.y <= 40){
                self.allowAnimation = false
                if(point.x > (bounds.size.width / 2)){
                    self.pickUpCustomMarker.groundAnchor = CGPoint(1.05, -0.05)// Bottom-Left
                }else{
                    self.pickUpCustomMarker.groundAnchor = CGPoint(-0.05, -0.05) //Bootom-Right
                }
                
            }else if(point.y >= bounds.size.height - self.requestPickUpView.frame.size.height - 40){
                self.allowAnimation = false
                if point.x > (bounds.size.width / 2){
                    
                    self.pickUpCustomMarker.groundAnchor = CGPoint(1.05, 1.05) // Top-Left
                }else{
                    self.pickUpCustomMarker.groundAnchor = CGPoint(-0.05, 1.05) // Top-Right
                }
            }
            CATransaction.commit()
            let coordDropoff = self.dropOffCustomMarker.position
            
            let pointDrop:CGPoint = self.gMapView.projection.point(for: coordDropoff)
            
            //CGPoint(-0.05, 1.05)// Top-Right ,CGPoint(1.05, -0.05)// Bottom-Left ,//CGPoint(1.05, 1.05) Top-Left ,//CGPoint(-0.05, -0.05) Bootom-Right
            
            if(pointDrop.x >= bounds.size.width - 110){
                if pointDrop.y > (bounds.size.height - self.requestPickUpView.frame.size.height) / 2{
                    
                    self.dropOffCustomMarker.groundAnchor = CGPoint(1.05, 1.05) // Top-Left
                    
                }else{
                    self.dropOffCustomMarker.groundAnchor = CGPoint(1.05, -0.05) // Bottom-Left
                }
                
            }else if(pointDrop.x <= 110){
                if point.y > (bounds.size.height - self.requestPickUpView.frame.size.height) / 2{
                    
                    self.dropOffCustomMarker.groundAnchor = CGPoint(-0.05, 1.05) // Top-Right
                    
                }else{
                    self.dropOffCustomMarker.groundAnchor = CGPoint(-0.05, -0.05) //Bootom-Right
                }
                
            }else if(pointDrop.y <= 40){
                if pointDrop.x > (bounds.size.width / 2){
                    self.dropOffCustomMarker.groundAnchor = CGPoint(1.05, -0.05)// Bottom-Left
                }else{
                    self.dropOffCustomMarker.groundAnchor = CGPoint(-0.05, -0.05) //Bootom-Right
                }
            }else if (pointDrop.y >= bounds.size.height - self.requestPickUpView.frame.size.height - 40){
                if(pointDrop.x > (bounds.size.width / 2)){
                    self.dropOffCustomMarker.groundAnchor = CGPoint(1.05, 1.05) // Top-Left
                }else{
                    self.dropOffCustomMarker.groundAnchor = CGPoint(-0.05, 1.05) // Top-Right
                }
            }
            
            if(self.pickUpLocation != nil && self.destLocation != nil && (abs(pointDrop.x - point.x) < 110 && abs(pointDrop.y - point.y) < 40)){

                if(self.pickUpCustomMarker.groundAnchor.x == self.dropOffCustomMarker.groundAnchor.x){
                    self.pickUpCustomMarker.groundAnchor.x = self.pickUpCustomMarker.groundAnchor.x == -0.05 ? 1.05 : -0.05
                }
                
                if(self.pickUpCustomMarker.groundAnchor.y == self.dropOffCustomMarker.groundAnchor.y){
//                    self.pickUpCustomMarker.groundAnchor.y = self.pickUpCustomMarker.groundAnchor.y == -0.05 ? 1.05 : -0.05
                }
            }
            
            
        }else{
            removeAnimLocations()
        }
    }
    
    func removeAnimLocations(){
        if(self.pickUpCustomMarker != nil){
            self.pickUpCustomMarker.map = nil
        }
        if(self.dropOffCustomMarker != nil){
            self.dropOffCustomMarker.map = nil
        }
        if(self.pickUpPinMarker != nil){
            self.pickUpPinMarker.map = nil
        }
        if(self.dropOffPinMarker != nil){
            self.dropOffPinMarker.map = nil
        }
    }
    
    func changeAddress(){
        var isSameLocation = false
        
        if(isPickUpMode){
            
            //            if(self.pickUpLocation != nil && self.pickUpLocation!.coordinate.latitude == getCenterLocation().coordinate.latitude && self.pickUpLocation!.coordinate.longitude == getCenterLocation().coordinate.longitude){
            if(self.pickUpLocation != nil && self.pickUpLocation.distance(from: getCenterLocation()) < 2.0 ){
                isSameLocation = true
            }else{
                self.addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                if(self.loadAvailableCab != nil){
                    self.loadAvailableCab.pickUpAddress = ""
                }
                
                //                self.pickUpAddGeoCodeResult = ""
            }
            
        }else{
            //            if(self.destLocation != nil && self.destLocation!.coordinate.latitude == getCenterLocation().coordinate.latitude && self.destLocation!.coordinate.longitude == getCenterLocation().coordinate.longitude){
            if(self.destLocation != nil && self.destLocation.distance(from: getCenterLocation()) < 2.0){
                isSameLocation = true
            }else{
                self.addressContainerView.destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                //                self.destAddGeoCodeResult = ""
            }
            
        }
        
        if(isSameLocation == false){
            getAddressFrmLocation.setLocation(latitude: getCenterLocation().coordinate.latitude, longitude: getCenterLocation().coordinate.longitude)
            getAddressFrmLocation.setPickUpMode(isPickUpMode: isPickUpMode)
            getAddressFrmLocation.executeProcess(isOpenLoader: false, isAlertShow:false)
            
            //            if(self.requestPickUpView != nil){
            //                estimateFare()
            //            }
        }else{
            if(self.tempPickUpAdd != ""){
                if(isPickUpMode){
                    self.addressContainerView.pickUpAddressLbl.text = self.tempPickUpAdd
                    if(self.loadAvailableCab != nil){
                        self.loadAvailableCab.pickUpAddress = self.tempPickUpAdd
                    }
                    
                    //                self.pickUpAddGeoCodeResult = self.tempPickUpGeoCodeResult
                }else{
                    self.addressContainerView.destAddressLbl.text = self.tempDestAdd
                    //                self.destAddGeoCodeResult = self.tempDestGeoCodeResult
                }
            }else{
                getAddressFrmLocation.setLocation(latitude: getCenterLocation().coordinate.latitude, longitude: getCenterLocation().coordinate.longitude)
                getAddressFrmLocation.setPickUpMode(isPickUpMode: isPickUpMode)
                getAddressFrmLocation.executeProcess(isOpenLoader: false, isAlertShow:false)
            }
        }
    }
    
    func getCenterLocation() -> CLLocation{
        return CLLocation(latitude: self.gMapView.camera.target.latitude, longitude: self.gMapView.camera.target.longitude)
    }
    
    
    func setData(){
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        self.userProfileJson = userProfileJson
        
        if(self.getPubNubConfig().uppercased() == "YES"){
            
            GeneralFunctions.postNotificationSignal(key: ConfigPubNub.pauseInst_key, obj: self)
            configPubNub = ConfigPubNub()
            configPubNub!.mainScreenUv = self
            configPubNub!.buildPubNub()
        }
        
        getAddressFrmLocation = GetAddressFromLocation(uv: self)
        getAddressFrmLocation.addressFoundDelegate = self
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        let userLocTapGue = UITapGestureRecognizer()
        userLocTapGue.addTarget(self, action: #selector(self.myLocImgTapped))
        self.myLocImgView.isUserInteractionEnabled = true
        self.myLocImgView.addGestureRecognizer(userLocTapGue)
        
        
        let setPrefTapGue = UITapGestureRecognizer()
        setPrefTapGue.addTarget(self, action: #selector(self.myPrefTapped))
        
        self.setPrefLocImgView.isUserInteractionEnabled = true
        self.setPrefLocImgView.addGestureRecognizer(setPrefTapGue)
        
        HANDICAP_ACCESSIBILITY_OPTION = userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION")
        FEMALE_RIDE_REQ_ENABLE = userProfileJson.get("FEMALE_RIDE_REQ_ENABLE")
        
        if((HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES" && FEMALE_RIDE_REQ_ENABLE.uppercased() != "YES") || (FEMALE_RIDE_REQ_ENABLE.uppercased() == "YES" && userProfileJson.get("eGender") == "Male" && HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES")){
            self.setPrefLocImgView.isHidden = true
        }
        else{
            self.setPrefLocImgView.isHidden = false
        }
        
        let menuImgTapGue = UITapGestureRecognizer()
        menuImgView.isUserInteractionEnabled = true
        menuImgTapGue.addTarget(self, action: #selector(self.openMenu))
        menuImgView.addGestureRecognizer(menuImgTapGue)
        
        GeneralFunctions.setImgTintColor(imgView: menuImgView, color: UIColor(hex: 0x000000))
        
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" || userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            if(currentCabGeneralType.uppercased() != Utils.cabGeneralType_UberX.uppercased()){
                let verifyBtn = Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), uv: self, btnTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_VERIFY_TXT"), delayShow: 1, delayHide: 15)
                verifyBtn.addTarget(self, action: #selector(self.openAccountVerify), for: UIControlEvents.touchUpInside)
            }
        }
        
        if(userProfileJson.get("APP_TYPE").uppercased() == "UBERX" && GeneralFunctions.hasLocationEnabled() == false){
            if(self.ufxSelectedLatitude != "" && self.ufxSelectedLongitude != ""){
                
                let ufxPickLoc = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLongitude))
                
                //                self.pickUpLocation = ufxPickLoc
                //                let camera = GMSCameraPosition.camera(withLatitude: ufxPickLoc.coordinate.latitude,
                //                                                      longitude: ufxPickLoc.coordinate.longitude, zoom: self.gMapView.camera.zoom)
                //
                //                self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
                
                self.onLocationUpdate(location: ufxPickLoc)
            }
        }
    }
    
    func openAccountVerify(){
        
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
    
    func setNavBar(isHidden:Bool){
        
        if(isHidden == true){
            self.navigationController?.navigationBar.isHidden = true
            self.menuImgView.isHidden = false
        }else{
            self.navigationController?.navigationBar.isHidden = false
            self.menuImgView.isHidden = true
            
            UIApplication.shared.statusBarStyle = UIStatusBarStyle.lightContent
            
            if(navItem != nil){
                let leftButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_menu_all_nav")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openMenu))
                self.navItem.leftBarButtonItem = leftButton
            }
            
        }
    }
    
    func setLblToTitleView(){
        
        if(self.navItem == nil){
            return
        }
        self.navItem.titleView = nil
        if(self.titleLbl == nil){
            titleLbl = MyLabel(frame: CGRect(x: 0, y:0, width: Application.screenSize.width, height: 50))
        }
        titleLbl.font = UIFont(name: "Roboto-Light", size: 20)!
        titleLbl.textAlignment = .center
        titleLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        self.navItem.titleView = titleLbl
    }
    
    func openMenu(){
        if(self.requestPickUpView != nil){
            removeAddReqPickUpView()
            return
        }
        
        continueOpenMenu()
        
    }
    
    func continueOpenMenu(){
        if(Configurations.isRTLMode()){
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: false)
            self.navigationDrawerController?.toggleRightView()
            
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: true)
        }else{
            self.navigationDrawerController?.toggleLeftView()
        }
    }
    
    func removeAddReqPickUpView(isDriverAssigned:Bool = false){
        if(self.requestPickUpView != nil){
            
            self.gMapView.frame.size.height = self.cntView.frame.size.height
            
            self.myLocBottomMargin.constant = 15
            
            self.addressContainerView.isHidden = false
            
            sourcePinImgViewWidth.constant = 50
            sourcePinImgViewHeight.constant = 50
            sourcePinImgViewOffset.constant = -25
            self.sourcePickUpEtaLbl.isHidden = true
            
            self.requestPickUpView.removeFromSuperview()
            self.requestPickUpView = nil
            self.menuImgView.image = UIImage(named: "ic_menu_all")!
            GeneralFunctions.setImgTintColor(imgView: menuImgView, color: UIColor(hex: 0x000000))
            
            self.stopAnimatePolyLine()
            
            
            if(self.currentCabGeneralType == Utils.cabGeneralType_UberX){
                self.addressContainerView.isHidden = true
            }
            
            sourcePinImgView.isHidden = true
            
            if(isDriverAssigned == false){
                self.destLocation = nil
                self.destAddress = ""
                self.appliedPromoCode = ""
                
                addressContainerView.destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
                
                self.selectedCabTypeId = getFirstCarTypeID()
                
                if(self.loadAvailableCab != nil && requestCabView == nil && isDriverAssigned == false){
                    self.loadAvailableCab.onResumeCalled()
                }
            }
            
            if(self.addressContainerView != nil){
                self.addressContainerView.isPickUpMode = false
                self.addressContainerView.pickUpTapped(isOpenSelection: false)
            }
            return
        }
    }
    
    func myPrefTapped(){
        if(self.userProfileJson.get("eGender") == "" && userProfileJson.get("FEMALE_RIDE_REQ_ENABLE").uppercased() == "YES"){
            openGenderView()
        }else{
            let openPrefOptionsView = OpenPrefOptionsView(uv: self, containerView: self.view)
            openPrefOptionsView.currentInst = openPrefOptionsView
            openPrefOptionsView.isPreferFemaleDriverEnable = self.isPreferFemaleDriverEnable
            openPrefOptionsView.isHandicapPrefEnabled = self.isHandicapPrefEnabled
            openPrefOptionsView.setViewHandler { (isPreferFemaleDriverEnable, isHandicapPrefEnabled) in
                self.isPreferFemaleDriverEnable = isPreferFemaleDriverEnable
                self.isHandicapPrefEnabled = isHandicapPrefEnabled
                
                if(self.loadAvailableCab != nil){
                    self.loadAvailableCab.changeCabs()
                }
            }
            
            openPrefOptionsView.show()
        }
        
    }
    
    func openGenderView(){
        
        genderView = self.generalFunc.loadView(nibName: "GenderView", uv: self, isWithOutSize: true)
        
        genderView.frame = CGRect(x: 0, y: 0, width: self.view.frame.width, height: self.view.frame.height)
        
        self.view.addSubview(genderView)
        
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
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
                    
                    self.userProfileJson = userProfileJson
                    
                    self.myPrefTapped()
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func myLocImgTapped(){
        
        if(self.pickUpPointMarker != nil){
            self.pickUpPointMarker.map = nil
            self.pickUpPointMarker = nil
        }
        
        if(self.destPointMarker != nil){
            self.destPointMarker.map = nil
            self.destPointMarker = nil
        }
        
        if(GeneralFunctions.hasLocationEnabled() == true || isDriverAssigned == true){
            
            if(isDriverAssigned == false){
                
                if(self.requestPickUpView == nil){
                    if(self.isDriverAssigned == false && self.addressContainerView != nil && self.currentLocation != nil && self.isPickUpMode){
                        //self.isPickUpMode &&
                        self.addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                        if(self.loadAvailableCab != nil){
                            self.loadAvailableCab.pickUpAddress = ""
                        }
                        
                        self.tempPickUpAdd = self.pickUpAddress
                        
                        let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation!.coordinate.latitude,
                                                              longitude: self.currentLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                        
                        self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
                        
                        self.changeAddress()
                        
                    }else if(currentLocation != nil){
                        
                        let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation!.coordinate.latitude,
                                                              longitude: self.currentLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                        
                        self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
                    }else if(pickUpLocation != nil){
                        
                        let camera = GMSCameraPosition.camera(withLatitude: self.pickUpLocation!.coordinate.latitude,
                                                              longitude: self.pickUpLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                        
                        self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
                    }
                }else if(self.pickUpLocation != nil && self.destLocation != nil){
                    self.boundMapFromSourceToDest()
                }else{
                    if(self.currentLocation == nil){
                        return
                    }
                    
                    let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation!.coordinate.latitude,
                                                          longitude: self.currentLocation!.coordinate.longitude, zoom: Utils.defaultZoomLevel)
                    
                    self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
                }
            }else{
                self.getAllCenter()
            }
            
        }else{
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GPSENABLE_TXT"))
        }
        
    }
    
    func boundMapFromSourceToDest(){
        let maxZoomLevel = self.gMapView.maxZoom
        self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: self.gMapView.maxZoom - 5)

        var bounds = GMSCoordinateBounds()
        bounds =  bounds.includingCoordinate(self.pickUpLocation.coordinate)
        if(self.destLocation != nil && self.destLocation.coordinate.latitude != 0.0){
            bounds =  bounds.includingCoordinate(self.destLocation.coordinate)
        }
        
        let update = GMSCameraUpdate.fit(bounds, withPadding: 60)
        
        CATransaction.begin()
        CATransaction.setCompletionBlock {
            self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: maxZoomLevel)
        }
        
        self.gMapView.animate(with: update)
        
        CATransaction.commit()
    }
    
    func onLocationUpdate(location: CLLocation) {
        if(gMapView == nil){
            releaseAllTask()
            return
        }
        
        if(self.ufxSelectedLatitude != "" && self.ufxSelectedLongitude != ""){
            
            let ufxPickLoc = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLongitude))
            
            self.currentLocation = ufxPickLoc
        }else{
            self.currentLocation = location
        }
        
        
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        if(isFirstLocationUpdate == true){
            //             || self.isDriverAssigned == true || self.requestPickUpView == nil
            let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                                  longitude: location.coordinate.longitude, zoom: currentZoomLevel)
            
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            
            if(self.pickUpLocation == nil){
                self.pickUpLocation = currentLocation
                initializeView()
            }
            
        }
        
        checkLocationEnabled()
        
//        if(isFirstLocationUpdate){
////            initializeView()
//        }
        
        isFirstLocationUpdate = false
        
    }
    
    
    func onAddressFound(address: String, location: CLLocation, isPickUpMode:Bool, dataResult:String) {
        if(isPickUpMode){
            addressContainerView.pickUpAddressLbl.text = address
            self.pickUpLocation = location
            self.pickUpAddress = address

            //            self.pickUpAddGeoCodeResult = dataResult

            var isFirstSearch = false

            if(loadAvailableCab == nil){
                loadAvailableCab = LoadAvailableCab(gMapView: self.gMapView, mainScreenUv: self)

                loadAvailableCab.pickUpAddress = address
                //                loadAvailableCab.currentGeoCodeResult = dataResult


                self.selectedCabTypeId = getFirstCarTypeID()

                if(pickUpLocation == nil){
                    self.pickUpLocation = self.currentLocation!
                }
                loadAvailableCab.setCabTypeId(selectedCabTypeId: self.selectedCabTypeId)
                loadAvailableCab.setPickUpLocation(pickUpLocation: location)
                loadAvailableCab.changeCabs()

                isFirstSearch = true
            }

            if(self.loadAvailableCab != nil && isFirstSearch == false){
                self.loadAvailableCab.setPickUpLocation(pickUpLocation: location)
                self.loadAvailableCab.pickUpAddress = address
                //                self.loadAvailableCab.currentGeoCodeResult = dataResult
                self.loadAvailableCab.changeCabs()
                //                if(isPickUpAddressStateChanged(address: address) == true){
                //                    self.loadAvailableCab.checkAvailableCabs()
                //                }

                if(self.cabTypesArr.count < 1){
                    if(self.loadAvailableCab != nil){
                        self.loadAvailableCab.checkAvailableCabs()
                    }
                }else{
                    self.isPickUpAddressStateChanged(pickUpLocation: location)
                }
            }

            if(isFirstAddress == true){
                self.gMapView.delegate = self
                isFirstAddress = false

                addressContainerView.destViewTapped(isAutoOpenSelection: false)
            }
            
        }else{
            
            //            self.destAddGeoCodeResult = dataResult
            
            self.destLocation = location
            self.destAddress = address
            
            addressContainerView.destAddressLbl.text = address
        }
        
        
        if(requestPickUpView != nil){
            self.cabTypesFareArr.removeAll()
            self.cabTypeCollectionView.reloadData()
//            estimateFare()
            if(self.destAddress != "DEST_SKIPPED"){
                self.estimateFare()
            }
        }
    }
    
    func isPickUpAddressStateChanged(pickUpLocation:CLLocation){
        
        let parameters = ["type":"CheckSourceLocationState","PickUpLatitude": "\(pickUpLocation.coordinate.latitude)", "PickUpLongitude": "\(pickUpLocation.coordinate.latitude)", "SelectedCarTypeID": self.getAvailableCarTypesIds(), "CurrentCabGeneralType": self.currentCabGeneralType]
        //        , "TimeZone": selectedTimeZone
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    if(self.loadAvailableCab != nil){
                        self.loadAvailableCab.checkAvailableCabs()
                    }
                }
                
            }else{
                if(self.loadAvailableCab != nil){
                    self.loadAvailableCab.checkAvailableCabs()
                }
            }
        })
    }
    
    //    func isPickUpAddressStateChanged(address:String) -> Bool{
    //        let addressComponents = address.components(separatedBy: ",")
    //
    //        var isCountryMatched = false
    //        var isCityMatched = false
    //
    //        for i in 0..<addressComponents.count{
    //            let addr = addressComponents[i].trim()
    //
    //            if(self.currentPickUpCountry != "" && self.currentPickUpCountry.containsIgnoringCase(find: addr)){
    //                isCountryMatched = true
    //            }
    //
    //            if(self.currentPickUpCity != "" && self.currentPickUpCity.containsIgnoringCase(find: addr)){
    //                isCityMatched = true
    //            }
    //
    //        }
    //
    //        if(isCountryMatched == false || isCityMatched == false){
    //            return true
    //        }
    //
    //        return false
    //    }
    
    func releaseAllTask(isDismiss:Bool = true){
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        if(getAddressFrmLocation != nil){
            getAddressFrmLocation!.addressFoundDelegate = nil
            getAddressFrmLocation = nil
        }
        
        if(self.loadAvailableCab != nil){
            self.loadAvailableCab.onPauseCalled()
            self.loadAvailableCab.gMapView = nil
            self.loadAvailableCab = nil
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
        
        if(self.updateDirection != nil){
            self.updateDirection.releaseTask()
            self.updateDirection.onDirectionUpdateDelegate = nil
            self.updateDirection = nil
        }
        
        if(self.updateFreqDriverLocTask != nil){
            self.updateFreqDriverLocTask.stopRepeatingTask()
            self.updateFreqDriverLocTask.onTaskRunCalled = nil
            self.updateFreqDriverLocTask = nil
        }
        
        self.removeAllViewsRef(view: self.view)
        stopDriverRequestQueue()
        
        GeneralFunctions.removeObserver(obj: self)
        
        if(isDismiss){
            self.dismiss(animated: false, completion: nil)
            self.navigationController?.dismiss(animated: false, completion: nil)
            
        }
    }
    
    func removeAllViewsRef(view:UIView){
        let tempSubViews = view.subviews
        for i in 0..<tempSubViews.count
        {
            let tempView = tempSubViews[i]
            
            if let recognizers = tempView.gestureRecognizers {
                for recognizer in recognizers {
                    tempView.removeGestureRecognizer(recognizer)
                }
            }
            
            if(tempView.subviews.count > 0){
                self.removeAllViewsRef(view: tempView)
                continue
            }
            
            if(tempView as? MyButton != nil){
                (tempView as! MyButton).clickDelegate = nil
                //                tempView.removeFromSuperview()
                continue
            }
            if (tempView as? MyLabel != nil)
            {
                (tempView as! MyLabel).clickDelegate = nil
                //                tempView.removeFromSuperview()
                
                continue
            }
            if (tempView as? MyTextField != nil)
            {
                (tempView as! MyTextField).myTxtFieldDelegate = nil
                //                tempView.removeFromSuperview()
                continue
            }
            
            if(tempView as? UITableView != nil){
                (tempView as! UITableView).delegate = nil
                (tempView as! UITableView).dataSource = nil
            }
            
            if(tempView as? UICollectionView != nil){
                (tempView as! UICollectionView).delegate = nil
                (tempView as! UICollectionView).dataSource = nil
            }
            
            
            //            tempView.removeFromSuperview()
        }
    }
    
    
    func initializeView(){
        let vTripStatus = userProfileJson.get("vTripStatus")

        if(addressContainerView != nil){
            if(pickUpLocation != nil && userProfileJson.get("APP_TYPE").uppercased() != "UBERX" && (vTripStatus != "Active" && vTripStatus != "On Going Trip")){
                addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                
                getAddressFrmLocation.setLocation(latitude: pickUpLocation!.coordinate.latitude, longitude: pickUpLocation!.coordinate.longitude)
                getAddressFrmLocation.setPickUpMode(isPickUpMode: isPickUpMode)
                getAddressFrmLocation.executeProcess(isOpenLoader: false, isAlertShow:false)
            }
            return
        }
        
        addressContainerView = AddressContainerView(frame: CGRect(x: self.contentView.bounds.midX - ((Application.screenSize.width - 60) / 2), y: -140, width: Application.screenSize.width - 60, height: 100), mainScreenUv: self)
        
        addressContainerView.setViewHandler(handler: { (isPickUpMode, view) in
            self.isPickUpMode = isPickUpMode
            
            if(self.recentLocView != nil){
                self.recentLocView.checkRecentPlaces()
            }
        })
        
        self.cntView.addSubview(addressContainerView)
        
        if(userProfileJson.get("APP_TYPE").uppercased() == "UBERX"){
            self.addressContainerView.isHidden = true
        }
        addressBarYPosition = self.userProfileJson.get("APP_TYPE").uppercased() != "UBERX" ? (75 + GeneralFunctions.getSafeAreaInsets().top) : (20 + GeneralFunctions.getSafeAreaInsets().top)
        addressBarHeight = addressContainerView.frame.size.height
        
        UIView.animate(withDuration: 0.8, delay: 0.1, options: .transitionFlipFromTop,
                       animations: {
                        self.addressContainerView.frame.origin.y = self.addressBarYPosition
                        self.view.layoutIfNeeded()
        },  completion: { finished in
            self.addressContainerView.frame.origin.y = self.userProfileJson.get("APP_TYPE").uppercased() != "UBERX" ? (75 + GeneralFunctions.getSafeAreaInsets().top) : (20 + GeneralFunctions.getSafeAreaInsets().top)
            self.view.layoutIfNeeded()
        })
        
        if(userProfileJson.get("APP_TYPE").uppercased() != "UBERX" && (vTripStatus == "Active" || vTripStatus == "On Going Trip")){
            
            setNavBar(isHidden: false)
            setLblToTitleView()
            self.titleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: vTripStatus == "Active" ? "LBL_ARRIVING_TXT" : "LBL_EN_ROUTE_TXT")
            
            configureAssignedDriver(isAppRestarted: true)
            if(self.gMapView != nil){
                self.gMapView.delegate = self
            }
            //            self.gMapView.padding = UIEdgeInsetsMake(self.addressBarYPosition + 15, 0, 0, 0)
            
        }else{
            
                self.menuImgView.isHidden = false

            
            if(self.userProfileJson.get("APP_TYPE").uppercased() == "DELIVERY"){
                self.currentCabGeneralType = Utils.cabGeneralType_Deliver
            }else if(self.userProfileJson.get("APP_TYPE").uppercased() == "UBERX"){
                self.currentCabGeneralType = Utils.cabGeneralType_UberX
            }
            
            
            if(pickUpLocation != nil){
                addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
                
                getAddressFrmLocation.setLocation(latitude: pickUpLocation!.coordinate.latitude, longitude: pickUpLocation!.coordinate.longitude)
                getAddressFrmLocation.setPickUpMode(isPickUpMode: isPickUpMode)
                getAddressFrmLocation.executeProcess(isOpenLoader: false, isAlertShow:false)
            }else{
                addressContainerView.pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_PICKUP_LOC")
            }
            
            
            addDriverNotificationObserverForRideLater()
            
            
            /* add Recent Location View if APP_TYPE != 'UFX'*/
            
            if(self.userProfileJson.get("APP_TYPE").uppercased() != Utils.cabGeneralType_UberX){
                //                defaultRecentLocationViewHeight = 50
                ////                + (Application.statusBarHeight > Application.statusBarHeight ? Application.statusBarHeight : 0)
                //                self.setPrefImgViewBottomMargin.constant = self.setPrefImgViewBottomMargin.constant + defaultRecentLocationViewHeight
                //                self.myLocBottomMargin.constant = self.myLocBottomMargin.constant + defaultRecentLocationViewHeight
                //
                //                recentLocView = RecentLocationView(frame: CGRect(x:0, y:0, width:0, height: 0))
                //                recentLocView.mainScreenUV = self
                //                recentLocView.initializeDragView(dragViewAnimatedTopSpace: self.addressContainerView.frame.origin.y + self.addressContainerView.frame.size.height + 20, viewDefaultHeightConstant: defaultRecentLocationViewHeight, containerView: self.view)
                //                self.view.addSubview(recentLocView)
            }
            
            /* complete Recent Location View if APP_TYPE != 'UFX'*/
            var offsetHeight:CGFloat = 50
//            if(self.setPrefLocImgView.isHidden == false){
//                 offsetHeight = 50
//            }
            
            
        }
    }
    
    
    
    func addressViewTapped(sender:UITapGestureRecognizer){
        
        if(pickUpAddViewFrame == nil){
            pickUpAddViewFrame = addressContainerView.subviews[1].frame
        }
        
        if(destAddViewFrame == nil){
            destAddViewFrame = addressContainerView.subviews[0].frame
        }
        
        if(isPickUpMode == false){
            
            if(sender.view!.tag == 0 && isPickUpMode == false){
                if(self.isDriverAssigned == true && self.destLocation != nil){
                }else{
                    let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
                    addDestinationUv.centerLocation = self.pickUpLocation
                    addDestinationUv.SCREEN_TYPE = "DESTINATION"
                    self.pushToNavController(uv: addDestinationUv)
                }
                
                return
            }
            
            addressContainerView.subviews[0].layer.zPosition = -1
            addressContainerView.subviews[1].layer.zPosition = 1
            
            addressContainerView.subviews[0].frame.size = CGSize(width: Application.screenSize.width - 60, height: destAddViewFrame.height)
            addressContainerView.subviews[0].layoutIfNeeded()
            addressContainerView.subviews[0].setNeedsLayout()
            
            addressContainerView.subviews[1].frame.size = CGSize(width: Application.screenSize.width - 60 - 25, height: pickUpAddViewFrame.height)
            addressContainerView.subviews[1].layoutIfNeeded()
            addressContainerView.subviews[1].setNeedsLayout()
            addressContainerView.layoutSubviews()
            
            isPickUpMode = true
            
        }else{
            
            if(sender.view!.tag == 1 && isPickUpMode == true){
                if(self.isDriverAssigned == false){
                    
                    let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
                    addDestinationUv.centerLocation = self.pickUpLocation
                    addDestinationUv.SCREEN_TYPE = "PICKUP"
                    self.pushToNavController(uv: addDestinationUv)
                }
                return
            }
            
            addressContainerView.subviews[0].frame.size = CGSize(width: Application.screenSize.width - 60 - 25, height: pickUpAddViewFrame.height)
            addressContainerView.subviews[0].layoutIfNeeded()
            addressContainerView.subviews[0].setNeedsLayout()
            
            addressContainerView.subviews[1].frame.size = CGSize(width: Application.screenSize.width - 60, height: destAddViewFrame.height)
            addressContainerView.subviews[1].layoutIfNeeded()
            addressContainerView.subviews[1].setNeedsLayout()
            
            addressContainerView.subviews[0].layer.zPosition = 1
            addressContainerView.subviews[1].layer.zPosition = -1
            
            addressContainerView.layoutSubviews()
            isPickUpMode = false
        }
        
    }
    
    func isCarTypesArrChanged(carTypes:NSArray) -> Bool{
        
        if(self.cabTypesArr.count != carTypes.count){
            return true
        }
        
        for i in 0..<self.cabTypesArr.count{
            let tempDict = self.cabTypesArr[i]
            let newTempDict = carTypes[i] as! NSDictionary
            
            let iVehicleTypeId = tempDict.get("iVehicleTypeId")
            let newVehicleTypeId = newTempDict.get("iVehicleTypeId")
            
            if(iVehicleTypeId != newVehicleTypeId){
                return true
            }
            
        }
        
        return false
    }
    
    func getFirstCarTypeID() -> String{
        var iVehicleTypeId = ""
        //        let carTypesArr = userProfileJson!.getArrObj("VehicleTypes")
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            return self.ufxSelectedVehicleTypeId
        }
        
        for i in 0..<cabTypesArr.count{
            let tempDict = cabTypesArr[i]
            
            //            if(tempDict.get("eType") == self.userProfileJson.get("APP_TYPE")){
            
            iVehicleTypeId = tempDict.get("iVehicleTypeId")
            
            break
            //            }
            
        }
        
        return iVehicleTypeId
    }
    
    func notifyCarSearching(){
        
        
        
        setETA(time: "--")
    }
    
    func notifyNoCabs(){
        
        
        setCurrentLoadedDriverList(currentLoadedDriverList: [])
        
        if(self.requestPickUpView != nil){
        }
        setETA(time: "--")
    }
    
    func getAllCenter(){
        if(self.assignedDriverLocation == nil){
            return
        }
        
        if(self.pickUpPointMarker != nil){
            self.pickUpPointMarker.map = nil
            self.pickUpPointMarker = nil
        }
        
        if(self.destPointMarker != nil){
            self.destPointMarker.map = nil
            self.destPointMarker = nil
        }
        
        var isUpwardLocation = false
        let driverLocPoint = self.gMapView.projection.point(for: self.assignedDriverLocation.coordinate)

        if(isTripStarted){
            if(self.assignedDriverLocation != nil && self.destLocation != nil){
               let destLocPoint =  self.gMapView.projection.point(for: self.destLocation.coordinate)
                
                if(driverLocPoint.y > destLocPoint.y){
                    isUpwardLocation = true
                }
            }
        }else{
            if(self.assignedDriverLocation != nil && self.pickUpLocation != nil){
                let pickUpLocPoint =  self.gMapView.projection.point(for: self.pickUpLocation.coordinate)
                
                if(driverLocPoint.y > pickUpLocPoint.y){
                    isUpwardLocation = true
                }
            }
        }
        
        var edgeInsets:UIEdgeInsets!
        
        if(isUpwardLocation == true){
            edgeInsets = UIEdgeInsetsMake(self.addressBarYPosition + addressBarHeight + heightOfWaitingMarker, 20, (UIScreen.main.scale == 3 ? 64 : 32) + 20, 20)
        }else{
            edgeInsets = UIEdgeInsetsMake(self.addressBarYPosition + addressBarHeight, UIScreen.main.scale == 3 ? 60 : 20, 20 + 20, 20)
        }
        
        let maxZoomLevel = self.gMapView.maxZoom
        if(isTripStarted == false){
            if(self.assignedDriverLocation != nil && self.assignedDriverLocation.coordinate.latitude != 0.0 && self.assignedDriverLocation.coordinate.longitude != 0.0 && self.pickUpLocation != nil){
                var bounds = GMSCoordinateBounds()
                bounds = bounds.includingCoordinate(self.assignedDriverLocation.coordinate)
                bounds = bounds.includingCoordinate(self.pickUpLocation.coordinate)
                if(self.isDriverArrived == true && self.addressContainerView != nil){
                    self.addressContainerView.addPickUpMarker(isMoveToPickUP: false)
                }
                self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: self.gMapView.maxZoom - 5)
                
                CATransaction.begin()
                CATransaction.setCompletionBlock {
                    self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: maxZoomLevel)
                }
                self.gMapView.animate(with: GMSCameraUpdate.fit(bounds, with: edgeInsets))
                CATransaction.commit()
            }
        }else{
            if(self.assignedDriverLocation != nil && self.assignedDriverLocation.coordinate.latitude != 0.0 && self.assignedDriverLocation.coordinate.longitude != 0.0 && self.destLocation != nil){
                var bounds = GMSCoordinateBounds()
                bounds = bounds.includingCoordinate(self.assignedDriverLocation.coordinate)
                if(destAddress != ""){
                    bounds = bounds.includingCoordinate(self.destLocation.coordinate)
                }
                
                self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: self.gMapView.maxZoom - 5)
                
                CATransaction.begin()
                CATransaction.setCompletionBlock {
                   self.gMapView.setMinZoom(self.gMapView.minZoom, maxZoom: maxZoomLevel)
                }
                self.gMapView.animate(with: GMSCameraUpdate.fit(bounds, with: edgeInsets))
                CATransaction.commit()
            }
        }
        
    }
    func notifyCabsAvailable(){
        
      
    }
    
    func setCurrentLoadedDriverList(currentLoadedDriverList: [NSDictionary]){
        self.currentLoadedDriverList = currentLoadedDriverList
    }
    
    func getDriverLocationChannelList() -> [String]{
        
        var subscribeChannelList = [String]()
        
        for i in 0..<currentLoadedDriverList.count{
            subscribeChannelList += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER + "\(currentLoadedDriverList[i].get("driver_id"))"]
        }
        
        return subscribeChannelList
    }
    
    func getDriverLocationChannelList(listData:[NSDictionary]) -> [String]{
        
        var channels_update_loc = [String]()
        
        for i in 0..<listData.count{
            channels_update_loc += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER + "\(listData[i].get("driver_id"))"]
        }
        
        return channels_update_loc
    }
    
    func getDriverMarkerOnPubNubMsg(iDriverId:String, isRemoveFromList:Bool) -> GMSMarker?{
        if(loadAvailableCab == nil){
            return nil
        }
        var driverMarkerList = self.loadAvailableCab.getDriverMarkerList()
        
        for i in 0..<driverMarkerList.count{
            let marker = driverMarkerList[i]
            let driver_id = marker.title?.replace("DriverId", withString: "")
            if(driver_id == iDriverId){
                if(isRemoveFromList){
                    driverMarkerList.remove(at: i)
                    self.loadAvailableCab.setDriverMarkerList(driverMarkerList: driverMarkerList)
                }
                
                return marker
            }
        }
        
        return nil
    }
    
    func setETA(time:String){
        if(sourcePickUpEtaLbl != nil){
            sourcePickUpEtaLbl.text = time
        }
        if(sourcePinImgView.isHidden == true){
            self.sourcePickUpEtaLbl.isHidden = true
        }
        
        //        if(requestPickUpView != nil && pickUpMarkerView != nil){
        //            addMarker()
        //        }
        
        if(requestPickUpView != nil && pickUpCustomMarker != nil){
            addPickUpMarkerWithTimeLbl()
            self.pickUpCustomMarker.zIndex = 1
            self.pickUpCustomMarker.map = self.gMapView
        }
    }
    
    func refreshRequestPickUpView(){
        
        if(requestPickUpView != nil){
            var height:CGFloat = 255
            if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                height = height - 134
            }
            
            requestPickUpView.frame = CGRect(x: 0, y: self.contentView.frame.size.height + height, width: Application.screenSize.width, height: height)
            self.requestPickUpView.frame.origin.y = self.contentView.frame.size.height - height
        }
    }
    
    func showRequestPickUpLoaderView(){
        if(requestPickUpLoaderContainerView != nil && requestPickUpLoaderContainerView.subviews.count > 0 && requestPickUpLoaderContainerView.subviews[0].isKind(of: LinearProgressBarView.self)){
            (requestPickUpLoaderContainerView.subviews[0] as! LinearProgressBarView).startAnimation()
            requestPickUpLoaderContainerView.isHidden = false
            requestNowBtnEnabled = self.requestNowBtn.isBtnEnabled
            self.requestNowBtn.setButtonEnabled(isBtnEnabled: false)
        }
    }
    
    func hideRequestPickUpLoaderView(){
        if(requestPickUpLoaderContainerView != nil && requestPickUpLoaderContainerView.subviews.count > 0 && requestPickUpLoaderContainerView.subviews[0].isKind(of: LinearProgressBarView.self)){
            (requestPickUpLoaderContainerView.subviews[0] as! LinearProgressBarView).stopAnimation()
            requestPickUpLoaderContainerView.isHidden = true
            self.requestNowBtn.setButtonEnabled(isBtnEnabled: requestNowBtnEnabled)
        }
    }
    
    func openRequestPickUpView(isFromRideLater:Bool){
        
        //        self.addressContainerView.isHidden = false
        
        self.addressContainerView.isHidden = true
        
//        estimateFare()
        //        sourcePinImgView.isHidden = false
        sourcePinImgView.isHidden = true
        requestPickUpView = self.generalFunc.loadView(nibName: "RequestPickUpBottomView", uv: self, isWithOutSize: true)
        
        var height:CGFloat = 255 + GeneralFunctions.getSafeAreaInsets().bottom
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            height = height - 134
        }
        
        requestPickUpView.frame = CGRect(x: 0, y: self.cntView.frame.size.height, width: Application.screenSize.width, height: height)
        self.cardPaymentView.isHidden = true
        //        requestPickUpView.center = CGPoint(x: self.view.bounds.midX, y: Application.screenSize.height - 310)
        self.cntView.addSubview(requestPickUpView)
        
        
        let linearProgressBar = LinearProgressBarView()
        
        linearProgressBar.heightForLinearBar = 5
        linearProgressBar.backgroundColor = UIColor.clear
        linearProgressBar.backgroundProgressBarColor = self.requestPickUpLoaderContainerView.backgroundColor != nil ? self.requestPickUpLoaderContainerView.backgroundColor! : UIColor.clear
        linearProgressBar.progressBarColor = UIColor.UCAColor.AppThemeColor
        linearProgressBar.stopAnimation()
        
        self.requestPickUpLoaderContainerView.addSubview(linearProgressBar)
        
        
        let requestPickUpViewFinalHeight = self.cntView.frame.size.height - height
        let locBottomFinalMargin = self.myLocBottomMargin.constant + height
        
        
        //        UIView.animate(withDuration: 1.1,
        //                       animations: {
        //                        self.gMapView.frame.size.height = self.cntView.frame.size.height - height
        //                        self.view.layoutIfNeeded()
        //        },  completion: { finished in
        //            self.gMapView.frame.size.height = self.cntView.frame.size.height - height
        //            self.view.layoutIfNeeded()
        //        })
        
        UIView.animate(withDuration: 0.8,
                       animations: {
                        self.requestPickUpView.frame.origin.y = requestPickUpViewFinalHeight
                        self.myLocBottomMargin.constant = locBottomFinalMargin
                        self.gMapView.frame.size.height = self.cntView.frame.size.height - height
                        self.view.layoutIfNeeded()
        },  completion: { finished in
            
            self.requestPickUpView.frame.origin.y = requestPickUpViewFinalHeight
            self.myLocBottomMargin.constant = locBottomFinalMargin
            self.gMapView.frame.size.height = self.cntView.frame.size.height - height
            self.view.layoutIfNeeded()
        })
        
        self.cashPayLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pay by cash", key: "LBL_PAY_BY_CASH_TXT")
        self.cardPayLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pay by card", key: "LBL_PAY_BY_CARD_TXT")
        
        self.promoCodeLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PRMO_TXT")
        self.noCabTypeLbl.text = self.generalFunc.getLanguageLabel(origValue: "No aervice available in your selected pickup location.", key: "LBL_NO_SERVICE_AVAILABLE_TXT")
        
        self.noCabTypeLbl.fitText()
        self.noCabTypeLbl.isHidden = true
        
        if(self.cabTypesArr.count < 1){
            self.noCabTypeLbl.isHidden = false
        }
        
        let promoTapGue = UITapGestureRecognizer()
        promoTapGue.addTarget(self, action: #selector(self.promoTapped))
        
        self.promoCodeView.addGestureRecognizer(promoTapGue)
        
        self.isCardValidated = false
        self.isCashPayment = true
        self.isDeliveryDataEntered = false
        
        self.receiverName = ""
        self.receiverMobile = ""
        self.pickUpIns = ""
        self.deliveryIns = ""
        self.packageTypeId = ""
        self.packageDetails = ""
        
        self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CASH_TXT")
        
        if(self.userProfileJson.get("APP_PAYMENT_MODE") == "Cash-Card"){
            self.payView.tag = 0
            self.payView.isUserInteractionEnabled = true
            self.payView.addGestureRecognizer(getPayTapGue())
            
            self.cashPaymentView.tag = 1
            self.payView.isUserInteractionEnabled = true
            self.cashPaymentView.addGestureRecognizer(getPayTapGue())
            
            self.cardPaymentView.tag = 2
            self.payView.isUserInteractionEnabled = true
            self.cardPaymentView.addGestureRecognizer(getPayTapGue())
        }else if(self.userProfileJson.get("APP_PAYMENT_MODE") == "Card"){
            isCashPayment = false
            self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD")
            self.payImgView.image = UIImage(named: "ic_card_new")
        }else{
            isCashPayment = true
            self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CASH_TXT")
            self.payImgView.image = UIImage(named: "ic_cash_new")
        }
        
        if(self.currentCabGeneralType == Utils.cabGeneralType_Deliver){
            self.requestNowBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Next", key: "LBL_BTN_NEXT_TXT"))
            
            self.rideLaterImgView.image = UIImage(named: "ic_delivery_later")
        }else{
            self.requestNowBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Request Now", key: "LBL_REQUEST_NOW"))
            self.rideLaterImgView.image = UIImage(named: "ic_ride_later")
        }
        
        self.cabTypeCollectionView.register(UINib(nibName: "CabTypeCVCell", bundle: nil), forCellWithReuseIdentifier: "CabTypeCVCell")
        self.cabTypeCollectionView.dataSource = self
        self.cabTypeCollectionView.delegate = self
        self.cabTypeCollectionView.reloadData()
        self.cabTypeCollectionView.bounces = false
        self.cabTypeCollectionView.contentInset = UIEdgeInsets(top: 0, left: 5, bottom: 0, right: 5)
        
        self.menuImgView.image = UIImage(named: "ic_nav_bar_back")!
        GeneralFunctions.setImgTintColor(imgView: menuImgView, color: UIColor(hex: 0x000000))
        
        if(Configurations.isRTLMode()){
            self.menuImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
        }
        
        let rideLaterTapGue = UITapGestureRecognizer()
        
        rideLaterTapGue.addTarget(self, action: #selector(self.rideLaterTapped))
        
        rideLaterImgView.isUserInteractionEnabled = true
        rideLaterImgView.addGestureRecognizer(rideLaterTapGue)
        
        if(self.userProfileJson.get("RIDE_LATER_BOOKING_ENABLED").uppercased() != "YES"){
            self.rideLaterImgView.isHidden = true
            self.rideLaterImgViewWidth.constant = 0
            self.rideLaterImgViewLeading.constant = 0
        }
        
        self.requestNowBtn.clickDelegate = self
        
        if(currentLoadedDriverList.count == 0){
            
            //            self.requestNowBtn.setButtonEnabled(isBtnEnabled: false)
            //            self.requestNowBtn.setButtonTitleColor(color: UIColor(hex: 0x6b6b6b))
            self.requestNowBtn.setButtonTitleColor(color: UIColor.UCAColor.AppThemeTxtColor_1)
            self.requestNowBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "NO CARS", key: "LBL_NO_CARS"))
        }
        
        if(isFromRideLater == true){
            rideLaterImgView.isHidden = true
            self.rideLaterBtn.isHidden = false
            self.rideLaterBtn.clickDelegate = self
            self.rideLaterBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONFIRM_BOOKING"))
        }
        
      
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            self.cabTypeCollectionView.isHidden = true
            self.cabTypeContainerViewHeight.constant = 0
            self.noCabTypeLbl.isHidden = true
        }
        
        boundMapFromSourceToDest()
        
    }
    
    
    func estimateFare(){
        
        showRequestPickUpLoaderView()
        
        self.cabTypesFareArr.removeAll()
        if(cabTypeCollectionView != nil){
            self.cabTypeCollectionView.reloadData()
        }
        
        let destLoc = self.destLocation != nil ? self.destLocation : self.pickUpLocation
        
        let directionURL = "https://maps.googleapis.com/maps/api/directions/json?origin=\(self.pickUpLocation!.coordinate.latitude),\(self.pickUpLocation!.coordinate.longitude)&destination=\(destLoc!.coordinate.latitude),\(destLoc!.coordinate.longitude)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
        
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.view, isOpenLoader: false)
        
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            self.hideRequestPickUpLoaderView()
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                
                self.stopAnimatePolyLine()
                self.animLocationArr.removeAll()
                
                
                if(dataDict.get("status").uppercased() != "OK" || dataDict.getArrObj("routes").count == 0){
                    self.addMarker()
                    
                    self.isRouteDrawnFailed = true
                    
                    
                    if(self.directionFailedDialog != nil){
                        self.directionFailedDialog.disappear()
                        self.directionFailedDialog = nil
                    }
                    
                    self.directionFailedDialog = self.generalFunc.setAlertMessageWithReturnDialog(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DEST_ROUTE_NOT_FOUND"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                    })
                    
                    return
                }
                
                self.isRouteDrawnFailed = false
                
                if(self.directionFailedDialog != nil){
                    self.directionFailedDialog.disappear()
                    self.directionFailedDialog = nil
                }
                
                let routesArr = dataDict.getArrObj("routes")
                let legs_arr = (routesArr.object(at: 0) as! NSDictionary).getArrObj("legs")
                let duration = (legs_arr.object(at: 0) as! NSDictionary).getObj("duration").get("value")
                let distance = (legs_arr.object(at: 0) as! NSDictionary).getObj("distance").get("value")
                
                self.continueEstimateFare(distance: distance, time: duration)
                
                let points = (routesArr.object(at: 0) as! NSDictionary).getObj("overview_polyline").get("points")
                let animatedPath = GMSPath.init(fromEncodedPath: points)!
                
                //                self.perform(#selector(self.addMarker), with: self, afterDelay: 0.5)
                
                var i_anim:UInt = 0
                while (i_anim < animatedPath.count()) {
                    
                    self.animLocationArr.append(animatedPath.coordinate(at: i_anim))
                    i_anim += 1
                }
                
                self.animatePolyLine()
                
                //                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                
                self.addMarker()
                //                })
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
        }, url: directionURL)
        
    }
    
    func addPickUpMarkerWithTimeLbl(){
        
        let pickUpMarkerView = self.generalFunc.loadView(nibName: "PickUpMarkerView", uv: self, isWithOutSize: true)
        pickUpMarkerView.frame = CGRect(x: 0, y: 0, width: 110, height: 40)
        
        let timeLbl = UILabel(frame: CGRect(x: 2, y: 0, width: 36, height: 40))
        timeLbl.font = UIFont.systemFont(ofSize: 12)
        timeLbl.textAlignment = .center
        if(self.sourcePickUpEtaLbl != nil){
            timeLbl.text = self.sourcePickUpEtaLbl.text
        }else{
            timeLbl.text = "--"
        }
        
        timeLbl.textColor = UIColor.white
        timeLbl.backgroundColor = UIColor.black
        timeLbl.numberOfLines = 2
        pickUpMarkerView.addSubview(timeLbl)
        
        let label = UILabel(frame: CGRect(x: 42, y: 0, width: 66, height: 40))
        label.font = UIFont.systemFont(ofSize: 12)
        label.textAlignment = .center
        label.text = self.pickUpAddress
        label.textColor = UIColor(hex: 0x1e5b99)
        label.numberOfLines = 2
        pickUpMarkerView.addSubview(label)
        
        let markerImage = pickUpMarkerView.convertToImage()
        
        let markerView = UIImageView(image: markerImage)
        markerView.layer.shadowOpacity = 0.2
        markerView.layer.shadowColor = UIColor.black.cgColor
        markerView.layer.shadowRadius = 15
        
        var groundAnchorPoint = CGPoint(-0.0, -0.0) //CGPoint(-0.05, 1.05) Top-Right CGPoint(1.05, -0.05)// Bottom-Left //CGPoint(1.05, 1.05) Top-Left //CGPoint(-0.05, -0.05) Bootom-Right
        if(self.pickUpCustomMarker != nil){
            groundAnchorPoint = self.pickUpCustomMarker.groundAnchor
            self.pickUpCustomMarker.map = nil
        }
        
        self.pickUpCustomMarker = GMSMarker()
        self.pickUpCustomMarker.groundAnchor = groundAnchorPoint
        self.pickUpCustomMarker.position = CLLocationCoordinate2D(latitude: self.pickUpLocation!.coordinate.latitude, longitude: self.pickUpLocation!.coordinate.longitude)
        self.pickUpCustomMarker.iconView = markerView
        
    }
    
    func addMarker(){
        
        //creating a marker view For PickUp
        
        if(self.pickUpPinMarker != nil){
            self.pickUpPinMarker.map = nil
            self.pickUpPinMarker = nil
        }
        
        if(self.pickUpCustomMarker != nil){
            self.pickUpCustomMarker.map = nil
            self.pickUpCustomMarker = nil
        }
        
        let pickUpPin = UIImageView(image:UIImage.init(named: ("ic_my_location")))
        GeneralFunctions.setImgTintColor(imgView: pickUpPin, color: UIColor.black)
        self.pickUpPinMarker = GMSMarker()
        
        self.pickUpPinMarker.iconView = pickUpPin
        self.pickUpPinMarker.zIndex = -1
        self.pickUpPinMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        self.pickUpPinMarker.position = self.pickUpLocation!.coordinate
        self.pickUpPinMarker.map = self.gMapView
        
        
        self.addPickUpMarkerWithTimeLbl()
        self.pickUpCustomMarker.zIndex = 1
        self.pickUpCustomMarker.appearAnimation = GMSMarkerAnimation.pop
        self.pickUpCustomMarker.map = self.gMapView
        
        
        //creating a marker view For DropOff
        if(self.dropOffPinMarker != nil){
            self.dropOffPinMarker.map = nil
        }
        
        if(self.dropOffPinMarker != nil){
            self.dropOffPinMarker = nil
        }
        
        if(self.dropOffCustomMarker != nil){
            self.dropOffCustomMarker.map = nil
        }
        
        if(self.dropOffCustomMarker != nil){
            self.dropOffCustomMarker = nil
        }
        
        let dropPin = UIImageView(image:UIImage.init(named: ("ic_my_location")))
        GeneralFunctions.setImgTintColor(imgView: dropPin, color: UIColor.black)
        
        self.dropOffPinMarker = GMSMarker()
        self.dropOffPinMarker.iconView = dropPin
        self.dropOffPinMarker.zIndex = -1
        self.dropOffPinMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        self.dropOffPinMarker.position = self.destLocation.coordinate
        self.dropOffPinMarker.map = self.gMapView
        
        let dropOffMarkerview = self.generalFunc.loadView(nibName: "PickUpMarkerView", uv: self, isWithOutSize: true)
        dropOffMarkerview.frame = CGRect(x: 0, y: 0, width: 110, height: 40)
        let label2 = UILabel(frame: CGRect(x: 5, y: 0, width: 100, height: 40))
        label2.font = UIFont.systemFont(ofSize: 12)
        label2.textAlignment = .center
        label2.textColor = UIColor(hex: 0x333333)
        label2.text = self.destAddress
        dropOffMarkerview.addSubview(label2)
        
        // I have taken a pin image which is a custom image
        let dropmarkerImage = dropOffMarkerview.convertToImage()
        //creating a marker view
        let dropmarkerView = UIImageView(image: dropmarkerImage)
        dropmarkerView.layer.shadowOpacity = 0.5
        dropmarkerView.layer.shadowColor = UIColor.black.cgColor
        dropmarkerView.layer.shadowRadius = 10
        
        self.dropOffCustomMarker = GMSMarker()
        self.dropOffCustomMarker.groundAnchor = CGPoint(1.0, 1.0) //CGPoint(-0.05, 1.05) Top-Right CGPoint(1.05, -0.05)// Bottom-Left //CGPoint(1.05, 1.05) Top-Left //CGPoint(-0.05, -0.05) Bootom-Right
        self.dropOffCustomMarker.position = CLLocationCoordinate2D(latitude: self.destLocation.coordinate.latitude, longitude: self.destLocation.coordinate.longitude)
        self.dropOffCustomMarker.iconView = dropmarkerView
        self.dropOffCustomMarker.zIndex = 1
        self.dropOffCustomMarker.appearAnimation = GMSMarkerAnimation.pop
        self.dropOffCustomMarker.map = self.gMapView
    }
    
    func animatePolyLine(){
        self.stopAnimatePolyLine()
        
        if(animationPathOrig != nil){
            animationPathOrig.removeAllCoordinates()
        }
        
        if(animationPolylineOrig != nil){
            animationPolylineOrig.map = nil
        }
        var i_anim:Int = 0
        
        while(i_anim < self.animLocationArr.count){
            self.animationPathOrig.add(self.animLocationArr[i_anim])
            self.animationPolylineOrig.path = self.animationPathOrig
            self.animationPolylineOrig.strokeColor = UIColor.UCAColor.AppThemeColor_1
            self.animationPolylineOrig.strokeWidth = 5
            self.animationPolylineOrig.map = self.gMapView
            i_anim += 1
        }
        
        
        
        //        if self.animLocationArr.count < 40
        //        {
        //            self.createMultipleLocations()
        //        }
        
        //        var totalParts = Double(self.animLocationArr.count) / Utils.MAX_ROUTE_ANIM_INTERVAL
        //        totalParts = totalParts > 1 ? totalParts : 1
        //        let animLocArrParts = self.animLocationArr.chunked(by: Int(totalParts))
        //        self.animLocArrParts = animLocArrParts
        //
        //        let subSec = 1.0 / CGFloat((totalParts > 0 ? self.animLocArrParts[0].count : 1))
        
        let interval = Utils.MAX_ROUTE_ANIM_INTERVAL / Double(self.animLocationArr.count)
        animTask = UpdateFreqTask(interval: interval / 1000)
        animTask.currInst = animTask
        animTask.setTaskRunListener(onTaskRunCalled: self)
        
        animTask.startRepeatingTask()
    }
    
    func createMultipleLocations()
    {
        var finalLocArray = [CLLocationCoordinate2D]()
        for i in 0..<self.animLocationArr.count
        {
            
            let latitude = self.animLocationArr[i].latitude
            let longitude = self.animLocationArr[i].longitude
            if i+1 < self.animLocationArr.count // Check if last Object
            {
                let nextLatitude = self.animLocationArr[i + 1].latitude
                var differenceOfLat = 0.0
                if nextLatitude > latitude
                {
                    differenceOfLat = nextLatitude - latitude
                }
                else
                {
                    differenceOfLat = latitude - nextLatitude
                }
                let nextLongitude = self.animLocationArr[i + 1].longitude
                var differenceOfLong = 0.0
                if nextLongitude > longitude
                {
                    differenceOfLong = nextLongitude - longitude
                }
                else
                {
                    differenceOfLong = longitude - nextLongitude
                }
                
                let addableVal = differenceOfLat / 15
                let addableLongVal = differenceOfLong / 15
                
                finalLocArray.append(self.animLocationArr[i])
                
                
                for i in 0..<15
                {
                    
                    finalLocArray.append(CLLocationCoordinate2D(latitude: latitude + (addableVal * (Double(i) + 1.0)), longitude: longitude + (addableLongVal * (Double(i) + 1))))
                }
                
            }
            else
            {
                finalLocArray.append(self.animLocationArr[i])
            }
            
        }
        self.animLocationArr = finalLocArray
    }
    
    func stopAnimatePolyLine(){
        if(animTask != nil){
            animTask.stopRepeatingTask()
        }
        
        if(self.animationPath != nil){
            self.animationPath.removeAllCoordinates()
            self.animationPath = GMSMutablePath()
            self.animationPolyline.map = nil
        }
        
        if(animationPathOrig != nil){
            
            animationPathOrig.removeAllCoordinates()
            animationPathOrig = GMSMutablePath()
        }
        
        if(animationPolylineOrig != nil){
            animationPolylineOrig.map = nil
        }
        
        removeAnimLocations()
    }
    
    func continueEstimateFare(distance:String, time:String){
        var parameters = ["type":"estimateFareNew","SelectedCar": self.selectedCabTypeId, "distance": distance, "time": time, "iUserId": GeneralFunctions.getMemberd(), "PromoCode": self.appliedPromoCode, "SelectedCarTypeID": self.getAvailableCarTypesIds(), "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        if(self.pickUpLocation != nil){
            parameters["StartLatitude"] = "\(self.pickUpLocation.coordinate.latitude)"
            parameters["EndLongitude"] = "\(self.pickUpLocation.coordinate.longitude)"
        }
        
        if(self.destLocation != nil){
            parameters["DestLatitude"] = "\(self.destLocation.coordinate.latitude)"
            parameters["DestLongitude"] = "\(self.destLocation.coordinate.longitude)"
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            self.cabTypesFareArr.removeAll()
            if(self.cabTypeCollectionView != nil){
                self.cabTypeCollectionView.reloadData()
            }
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let cabTypesArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0..<cabTypesArr.count {
                        let tempItem = cabTypesArr[i] as! NSDictionary
                        
                        if(((tempItem.get("eType") == Utils.cabGeneralType_Ride) && (self.userProfileJson.get("APP_TYPE").uppercased() == "RIDE-DELIVERY" || self.userProfileJson.get("APP_TYPE").uppercased() == "RIDE")) ||
                            ((tempItem.get("eType") == Utils.cabGeneralType_Deliver) && (self.userProfileJson.get("APP_TYPE").uppercased() == "RIDE-DELIVERY" || self.userProfileJson.get("APP_TYPE").uppercased() == "DELIVERY"))){
                            self.cabTypesFareArr += [tempItem]
                        }
                    }
                    
                    
                    if(self.cabTypeCollectionView != nil){
                        self.cabTypeCollectionView.reloadData()
                    }
                    
                }else{
                    
                }
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    
    func getCabTypeFare(currentVehicleTypeId:String) -> String{
        
        for i in 0..<cabTypesFareArr.count {
            
            let tempDict = cabTypesFareArr[i]
            let iVehicleTypeId = tempDict.get("iVehicleTypeId")
            
            if(currentVehicleTypeId == iVehicleTypeId){
                return tempDict.get("total_fare")
            }
        }
        
        return ""
    }
    
    func getCabTypeFareValue(currentVehicleTypeId:String, key:String) -> String{
        
        for i in 0..<cabTypesFareArr.count {
            
            let tempDict = cabTypesFareArr[i]
            let iVehicleTypeId = tempDict.get("iVehicleTypeId")
            
            if(currentVehicleTypeId == iVehicleTypeId){
                return tempDict.get(key)
            }
        }
        
        return ""
    }
    
    
    func rideLaterTapped(){
        isDeliveryDataEntered = false
        
        if(destAddress == "DEST_SKIPPED"){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Destination is required to create scheduled booking.", key: "LBL_DEST_REQ_FOR_LATER"))
            return
        }
        
        if(isRouteDrawnFailed){
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DEST_ROUTE_NOT_FOUND"))
            return
        }
        
        if((self.userProfileJson.get("APP_PAYMENT_MODE") == "Card" && isCardValidated == false) || (isCashPayment == false && isCardValidated == false)){
            checkCardConfig(isRideLater: true, isAutoContinue: true)
            return
        }
        
        let minDate = Calendar.current.date(byAdding: .hour, value: 1, to: Date())
        let maxDate = Calendar.current.date(byAdding: .month, value: Utils.MAX_DATE_SELECTION_MONTH_FROM_CURRENT, to: Date())
        
        DatePickerDialog().show(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BOOKING_DATE"), doneButtonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_TXT"), cancelButtonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), minimumDate: minDate,maximumDate: maxDate, datePickerMode: .dateAndTime) {
            (date) -> Void in
            
            if(date != nil){
                let dateFormatter = DateFormatter()
                dateFormatter.locale = Locale(identifier: "en_GB")
                dateFormatter.dateFormat = "YYYY-MM-dd HH:mm"
                let dateString = dateFormatter.string(from: date!)
                
                self.selectedDate = dateString
                self.selectedTimeZone = dateFormatter.timeZone.identifier
                
                self.checkSurgePrice(selectedTime: dateString, isRideLater:true, ufxSelectedDriverIndex: -1)
            }
        }
    }
    
    
    func getGeoCode(pickUpLocation:CLLocation, destLocation:CLLocation, pickUpGeoCode:String, destGeoCode:String, selectedTime:String, isRideLater:Bool, ufxSelectedDriverIndex:Int){
        
        var location:CLLocation!
        
        if(pickUpGeoCode == ""){
            location = pickUpLocation
        }else{
            location = destLocation
        }
        
        if(loaderGeoCode == nil){
            DispatchQueue.main.async() {
                self.loaderGeoCode = self.generalFunc.showLoader(view: self.view)
            }
        }
        
        let geoCodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=\(location.coordinate.latitude),\(location.coordinate.longitude)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=ture"
        
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.view, isOpenLoader: false)
        
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("status").uppercased() != "OK" || dataDict.getArrObj("results").count == 0){
                    return
                }
                
                //                if(pickUpGeoCode == ""){
                //                    self.pickUpAddGeoCodeResult = response
                //                }else if(destGeoCode == ""){
                //                    self.destAddGeoCodeResult = response
                //                }
                //
                //                if(self.pickUpAddGeoCodeResult == "" || self.destAddGeoCodeResult == ""){
                //                    self.getGeoCode(pickUpLocation: pickUpLocation, destLocation: destLocation, pickUpGeoCode: self.pickUpAddGeoCodeResult, destGeoCode: self.destAddGeoCodeResult, selectedTime: selectedTime, isRideLater: isRideLater, ufxSelectedDriverIndex: ufxSelectedDriverIndex)
                //                }else{
                //                    if(self.loaderGeoCode != nil){
                //                        self.loaderGeoCode.hideDialog()
                //                        self.loaderGeoCode = nil
                //                    }
                //
                //                    self.checkSurgePrice(selectedTime: selectedTime, isRideLater: isRideLater, ufxSelectedDriverIndex: ufxSelectedDriverIndex)
                //                }
                
                
            }else{
                DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(2 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                    self.getGeoCode(pickUpLocation: pickUpLocation, destLocation: destLocation, pickUpGeoCode: pickUpGeoCode, destGeoCode: destGeoCode, selectedTime: selectedTime, isRideLater: isRideLater, ufxSelectedDriverIndex: ufxSelectedDriverIndex)
                })
            }
        }, url: geoCodeUrl)
        
    }
    
    func checkSurgePrice(selectedTime:String, isRideLater:Bool, ufxSelectedDriverIndex:Int){
        
        //        if(pickUpAddGeoCodeResult == "" || (userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "STRICT" && destAddGeoCodeResult == "") || (self.currentCabGeneralType != Utils.cabGeneralType_UberX && destAddGeoCodeResult == "") ){
        //            if(self.pickUpLocation != nil && self.destLocation != nil){
        //                self.getGeoCode(pickUpLocation: self.pickUpLocation, destLocation: self.destLocation, pickUpGeoCode: self.pickUpAddGeoCodeResult, destGeoCode: self.destAddGeoCodeResult, selectedTime: selectedTime, isRideLater: isRideLater, ufxSelectedDriverIndex: ufxSelectedDriverIndex)
        //            }
        //            return
        //        }
        
        
        
        let parameters = ["type":"checkSurgePrice","SelectedCarTypeID": self.selectedCabTypeId, "SelectedTime": selectedTime, "PickUpLatitude": "\(self.pickUpLocation!.coordinate.latitude)", "PickUpLongitude": "\(self.pickUpLocation!.coordinate.longitude)", "DestLatitude": "\(self.destLocation != nil ? "\(self.destLocation!.coordinate.latitude)" : "")", "DestLongitude": "\(self.destLocation != nil ? "\(self.destLocation!.coordinate.longitude)" : "")", "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        //        , "TimeZone": selectedTimeZone
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
               
                if(dataDict.get("Action") == "1"){
                    
                    if(isRideLater == false){
                        self.requestCab()
                    }else{
                        self.continueRideLaterSchedule( )
                    }
                    
                }else{
                    self.openSurgeConfirmDialog(isSurgeFromAddDestination: false, isRideLater: isRideLater, dataDict: dataDict)
                }
                
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
 
    
    /**
     This function is used to show surge charge view on screen.
     - parameters:
        - isSurgeFromAddDestination: By passing value true, This indicates whether a view opens while adding/change a destination. This will be called if trip will falled into flate trip after adding destination. User needs to confirm first for flate fare.
        - isRideLater: Pass true value if view is opening from booking later functionality (User is going to book an schedule trip/job).
        - dataDict: server response.
     */
    func openSurgeConfirmDialog(isSurgeFromAddDestination:Bool, isRideLater:Bool, dataDict:NSDictionary){
        self.isSurgeFromAddDestination = isSurgeFromAddDestination
        surgePriceView = self.generalFunc.loadView(nibName: "SurgePriceView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 390 ? 375 : Application.screenSize.width - 50
        
        var defaultHeight:CGFloat = 154
        surgePriceView.frame.size = CGSize(width: width, height: defaultHeight)

        surgePriceView.center = CGPoint(x: self.cntView.bounds.midX, y: self.cntView.bounds.midY)
        
        surgePriceBGView = UIView()
        surgePriceBGView.backgroundColor = UIColor.black
        self.surgePriceBGView.alpha = 0
        surgePriceBGView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
        surgePriceBGView.frame = self.cntView.frame
        
        surgePriceBGView.center = CGPoint(x: self.cntView.bounds.midX, y: self.cntView.bounds.midY)
        
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
        
        var currentFare = ""
        if(self.currentCabGeneralType != Utils.cabGeneralType_UberX){
            currentFare = Configurations.convertNumToAppLocal(numStr: self.getCabTypeFare(currentVehicleTypeId: self.selectedCabTypeId))
        }
        
        //        self.surgePayAmtLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PAYABLE_AMOUNT")
        self.surgePayAmtLbl.text = currentFare == "" ? "\(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PAYABLE_AMOUNT"))" : "\(self.generalFunc.getLanguageLabel(origValue: "Approx payable amount", key: "LBL_APPROX_PAY_AMOUNT")): \(currentFare)"
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
        surgePriceView.center = CGPoint(x: self.cntView.bounds.midX, y: self.cntView.bounds.midY)

        self.surgeAcceptBtn.clickDelegate = self
        
        self.isRideLaterFromSurge = isRideLater
    }
    
    /**
     This function is used to close or remove surge charge view from screen.
     */
    func cancelSurgeView(){
        surgePriceView.removeFromSuperview()
        surgePriceBGView.removeFromSuperview()
    }
    
    /**
     This function is used to show promo code view on screen.
     */
    func promoTapped(){
        
        promoCodeDialogView = self.generalFunc.loadView(nibName: "EnterPromoCodeView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 390 ? 375 : Application.screenSize.width - 50
        
        promoCodeDialogView.frame.size = CGSize(width: width, height: 200)
        
        
        promoCodeDialogView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        promoCodeBGDialogView = UIView()
        promoCodeBGDialogView.backgroundColor = UIColor.black
        promoCodeBGDialogView.alpha = 0.4
        promoCodeBGDialogView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
        //        promoCodeBGDialogView.frame = self.contentView.frame
        promoCodeBGDialogView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: Application.screenSize.height)
        
        //        promoCodeBGDialogView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        bgViewTapGue.addTarget(self, action: #selector(self.closePromoView))
        
        promoCodeBGDialogView.addGestureRecognizer(bgViewTapGue)
        
        promoCodeDialogView.layer.shadowOpacity = 0.5
        promoCodeDialogView.layer.shadowOffset = CGSize(width: 0, height: 3)
        promoCodeDialogView.layer.shadowColor = UIColor.black.cgColor
        
        
        self.view.addSubview(promoCodeBGDialogView)
        self.view.addSubview(promoCodeDialogView)
        
        promoCodeBGDialogView.alpha = 0
        promoCodeDialogView.alpha = 0
        
        UIView.animate(withDuration: 0.5,
                       animations: {
                        self.promoCodeBGDialogView.alpha = 0.4
                        self.promoCodeDialogView.alpha = 1
        },  completion: { finished in
            self.promoCodeBGDialogView.alpha = 0.4
            self.promoCodeDialogView.alpha = 1
        })
        
        
        let cancelPromoTapGue = UITapGestureRecognizer()
        cancelPromoTapGue.addTarget(self, action: #selector(self.closePromoView))
        
        cancelPromoLbl.isUserInteractionEnabled = true
        cancelPromoLbl.addGestureRecognizer(cancelPromoTapGue)
        
        let confirmPromoTapGue = UITapGestureRecognizer()
        confirmPromoTapGue.addTarget(self, action: #selector(self.checkPromoCode))
        
        confirmPromoLbl.isUserInteractionEnabled = true
        confirmPromoLbl.addGestureRecognizer(confirmPromoTapGue)
        
        self.promoViewHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROMO_CODE_ENTER_TITLE")
        self.cancelPromoLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT")
        self.confirmPromoLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT")
        
        Utils.createRoundedView(view: promoCodeDialogView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        if(self.appliedPromoCode != ""){
            self.promoTxtField.setText(text: self.appliedPromoCode)
        }
    }
    
    /**
     This function is used to close or remove promo code view from screen.
     */
    func closePromoView(){
        if(promoCodeBGDialogView != nil){
            promoCodeBGDialogView.removeFromSuperview()
            promoCodeBGDialogView = nil
        }
        
        if(promoCodeDialogView != nil){
            promoCodeDialogView.removeFromSuperview()
            promoCodeDialogView = nil
        }
        
    }
    
    /**
     This function is used to verify entered promo code - removed OR entered.
     */
    func checkPromoCode(){
        let required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        
        if(self.appliedPromoCode != "" && Utils.getText(textField: self.promoTxtField.getTextField()!) == ""){
            closePromoView()
            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROMO_REMOVED"))
            return
        }
        
        let promoEntered = Utils.checkText(textField: self.promoTxtField.getTextField()!) ? (Utils.getText(textField: self.promoTxtField.getTextField()!).contains(" ") ? Utils.setErrorFields(textField: self.promoTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PROMO_INVALIED")) : true) : Utils.setErrorFields(textField: self.promoTxtField.getTextField()!, error: required_str)
        
        if(promoEntered){
            applyPromoCodeView(appliedPromoCode: Utils.getText(textField: self.promoTxtField.getTextField()!))
        }
    }
    
    /**
     This function is used to check entered promo code is valid or not.
     - parameters:
        - appliedPromoCode: Entered promo code to be checked
     */
    func applyPromoCodeView(appliedPromoCode:String){
        
        closePromoView()
        
        let parameters = ["type":"CheckPromoCode","PromoCode": appliedPromoCode, "iUserId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                self.appliedPromoCode = ""
                if(dataDict.get("Action") == "1"){
                    
                    self.appliedPromoCode = appliedPromoCode
                    
                    if(self.requestPickUpView != nil){
                        self.cabTypesFareArr.removeAll()
                        self.cabTypeCollectionView.reloadData()
//                        self.estimateFare()
                        if(self.destAddress != "DEST_SKIPPED"){
                            self.estimateFare()
                        }
                    }
                    
                }
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func getPayTapGue() -> UITapGestureRecognizer{
        let payOptionTapGue = UITapGestureRecognizer()
        payOptionTapGue.addTarget(self, action: #selector(self.paymentOptionTapped(sender:)))
        
        return payOptionTapGue
    }
    
    func paymentOptionTapped(sender:UITapGestureRecognizer){
        
        if(sender.view!.tag == 0){
            
            self.requestPickUpView.frame.size = CGSize(width: self.requestPickUpView.frame.width, height: self.requestPickUpView.frame.height + 40)
            self.requestPickUpView.frame.origin.y = self.cntView.frame.height - self.requestPickUpView.frame.height
            
            UIView.animate(withDuration: 0.5,
                           animations: {
                            self.cardPaymentView.isHidden = false
                            self.cashPaymentView.isHidden = false
                            self.payView.isHidden = true
                            self.promoCodeView.isHidden = true
                            
                            self.paymentOptionContainerViewHeight.constant = self.paymentOptionContainerViewHeight.constant + 40
                            self.paymentOptionContainerView.frame.size = CGSize(width: self.paymentOptionContainerView.frame.width, height: self.paymentOptionContainerView.frame.height + 40)
                            
                            self.paymentContainerView.frame.size = CGSize(width: self.paymentOptionContainerView.frame.width, height: self.paymentOptionContainerView.frame.height + 40)
                            
                            self.payViewHeight.constant = 0
                            self.seperaterViewXPostion.constant = Configurations.isRTLMode() ? (self.seperaterViewXPostion.constant - Application.screenSize.width) : (self.seperaterViewXPostion.constant + Application.screenSize.width)
                            
            },  completion: { finished in
                
            })
        }else{
            
            self.requestPickUpView.frame.size = CGSize(width: self.requestPickUpView.frame.width, height: (self.requestPickUpView.frame.height - 40))
            self.requestPickUpView.frame.origin.y = self.cntView.frame.height - self.requestPickUpView.frame.height
            
            UIView.animate(withDuration: 0.5,
                           animations: {
                            
                            self.cardPaymentView.isHidden = true
                            self.cashPaymentView.isHidden = true
                            self.payView.isHidden = false
                            self.promoCodeView.isHidden = false
                            
                            self.paymentOptionContainerViewHeight.constant = self.paymentOptionContainerViewHeight.constant - 40
                            self.paymentOptionContainerView.frame.size = CGSize(width: self.paymentOptionContainerView.frame.width, height: self.paymentOptionContainerView.frame.height - 40)
                            
                            self.paymentContainerView.frame.size = CGSize(width: self.paymentOptionContainerView.frame.width, height: self.paymentOptionContainerView.frame.height - 40)
                            
                            self.payViewHeight.constant = 40
                            self.seperaterViewXPostion.constant = Configurations.isRTLMode() ? (self.seperaterViewXPostion.constant + Application.screenSize.width) : (self.seperaterViewXPostion.constant - Application.screenSize.width)
                            
            },  completion: { finished in
                
            })
            
            if(sender.view!.tag == 1){
                isCashPayment = true
                
                self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CASH_TXT")
                self.payImgView.image = UIImage(named: "ic_cash_new")
                
                if(self.loadAvailableCab != nil){
                    self.loadAvailableCab.changeCabs()
                }
            }else{
                //                isCashPayment = false
                //
                //                self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD")
                //                self.payImgView.image = UIImage(named: "ic_card_new")
                
                checkCardConfig(isRideLater: false, isAutoContinue: false)
            }
        }
        
        
    }
    
    func checkCardConfig(isRideLater:Bool, isAutoContinue:Bool){
        if(userProfileJson.get("vStripeCusId") == ""){
            let paymentUV = GeneralFunctions.instantiateViewController(pageName: "PaymentUV") as! PaymentUV
            paymentUV.isFromMainScreen = true
            (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(paymentUV, animated: true)
        }else{
            showPaymentBox(isRideLater:isRideLater, isAutoContinue: isAutoContinue)
        }
    }
    
    func changeCard(){
        closeConfirmCardView()
        let paymentUV = GeneralFunctions.instantiateViewController(pageName: "PaymentUV") as! PaymentUV
        paymentUV.isFromMainScreen = true
        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(paymentUV, animated: true)
    }
    
    
    func showPaymentBox(isRideLater:Bool, isAutoContinue:Bool){
        
        isRideLater_payBox = isRideLater
        isAutoContinue_payBox = isAutoContinue
        
        confirmCardDialogView = self.generalFunc.loadView(nibName: "ConfirmCardView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 390 ? 375 : Application.screenSize.width - 50
        
        confirmCardDialogView.frame.size = CGSize(width: width, height: 200)
        
        
        confirmCardDialogView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        confirmCardBGDialogView = UIView()
        confirmCardBGDialogView.backgroundColor = UIColor.black
        confirmCardBGDialogView.alpha = 0.4
        confirmCardBGDialogView.isUserInteractionEnabled = true
        
        let bgViewTapGue = UITapGestureRecognizer()
        confirmCardBGDialogView.frame = self.cntView.frame
        
        confirmCardBGDialogView.center = CGPoint(x: self.cntView.bounds.midX, y: self.cntView.bounds.midY)
        
        bgViewTapGue.addTarget(self, action: #selector(self.closeConfirmCardView))
        
        confirmCardBGDialogView.addGestureRecognizer(bgViewTapGue)
        
        confirmCardDialogView.layer.shadowOpacity = 0.5
        confirmCardDialogView.layer.shadowOffset = CGSize(width: 0, height: 3)
        confirmCardDialogView.layer.shadowColor = UIColor.black.cgColor
        
        
        self.cntView.addSubview(confirmCardBGDialogView)
        self.cntView.addSubview(confirmCardDialogView)
        
        confirmCardBGDialogView.alpha = 0
        confirmCardDialogView.alpha = 0
        
        UIView.animate(withDuration: 0.3,
                       animations: {
                        self.confirmCardBGDialogView.alpha = 0.4
                        self.confirmCardDialogView.alpha = 1
        },  completion: { finished in
            
            self.confirmCardBGDialogView.alpha = 0.4
            self.confirmCardDialogView.alpha = 1
        })
        
        let cancelConfirmCardTapGue = UITapGestureRecognizer()
        cancelConfirmCardTapGue.addTarget(self, action: #selector(self.closeConfirmCardView))
        
        cancelCardLbl.isUserInteractionEnabled = true
        cancelCardLbl.addGestureRecognizer(cancelConfirmCardTapGue)
        
        let confirmCardTapGue = UITapGestureRecognizer()
        confirmCardTapGue.addTarget(self, action: #selector(self.checkCard))
        
        confirmCardLbl.isUserInteractionEnabled = true
        confirmCardLbl.addGestureRecognizer(confirmCardTapGue)
        
        let changeCardTapGue = UITapGestureRecognizer()
        changeCardTapGue.addTarget(self, action: #selector(self.changeCard))
        
        changeCardLbl.isUserInteractionEnabled = true
        changeCardLbl.addGestureRecognizer(changeCardTapGue)
        
        self.confirmCardHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TITLE_PAYMENT_ALERT")
        
        Utils.createRoundedView(view: confirmCardDialogView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        self.confirmCardLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT")
        self.cancelCardLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT")
        self.changeCardLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHANGE")
        
        self.confirmCardVLbl.text = self.userProfileJson.get("vCreditCard")
    }
    
    
    func closeConfirmCardView(){
        if(confirmCardBGDialogView != nil){
            confirmCardBGDialogView.removeFromSuperview()
            confirmCardBGDialogView = nil
        }
        
        if(confirmCardDialogView != nil){
            confirmCardDialogView.removeFromSuperview()
            confirmCardDialogView = nil
        }
    }
    
    func setCardMode(){
        self.isCardValidated = true
        self.isCashPayment = false
        
        self.payLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD")
        self.payImgView.image = UIImage(named: "ic_card_new")
    }
    
    func checkCard(){
        closeConfirmCardView()
        
        let parameters = ["type":"CheckCard", "iUserId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.setCardMode()
                    
                    if(self.loadAvailableCab != nil){
                        self.loadAvailableCab.changeCabs()
                    }
                    
                    if(self.isAutoContinue_payBox == true){
                        if(self.isRideLater_payBox == true){
                            self.rideLaterTapped()
                        }else{
                            self.myBtnTapped(sender: self.requestNowBtn)
                        }
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        let iVehicleTypeId = self.cabTypesArr[indexPath.item].get("iVehicleTypeId")
        
        if(self.selectedCabTypeId == iVehicleTypeId){
            openFareInfoView(cabTypeItem: self.cabTypesArr[indexPath.item])
        }else{
            self.selectedCabTypeId = iVehicleTypeId
            self.loadAvailableCab!.setCabTypeId(selectedCabTypeId: iVehicleTypeId)
            self.loadAvailableCab!.changeCabs()
            collectionView.reloadData()
        }
        
    }
    
    func openFareInfoView(cabTypeItem: NSDictionary){
        let detailTxt = self.generalFunc.getLanguageLabel(origValue: "This fare is based on our estimation. This may vary during trip and final fare.", key: "LBL_GENERAL_NOTE_FARE_EST")
        
        var detailTxtHeight = detailTxt.height(withConstrainedWidth: Application.screenSize.width - 20.5, font: UIFont(name: "Roboto-Light", size: 17)!)
        
        let viewHeight = detailTxtHeight + 355 + (GeneralFunctions.getSafeAreaInsets().bottom / 2)
        
        self.menuImgView.isUserInteractionEnabled = false
        
        let height = Application.screenSize.height > viewHeight ? viewHeight : Application.screenSize.height
        
        
        if(viewHeight > Application.screenSize.height){
            detailTxtHeight = height - 355 + 20
            self.menuImgView.isHidden = true
        }
        
        //        height = height + GeneralFunctions.getSafeAreaInsets().bottom
        
        fareDetailView = FareDetailView(frame: CGRect(x: 0, y: self.cntView.frame.height + height, width: Application.screenSize.width, height: height))
        fareDetailBGView = UIView(frame: self.cntView.frame)
        fareDetailBGView.backgroundColor = UIColor.black
        fareDetailBGView.alpha = 0.4
        fareDetailBGView.isUserInteractionEnabled = true
        
        fareDetailView.setViewHandler { (isViewClose, view, isMoreDetailTapped) in
            self.fareDetailView.frame.origin.y = Application.screenSize.height + height
            self.fareDetailBGView.removeFromSuperview()
            self.fareDetailView.removeFromSuperview()
            self.cntView.layoutIfNeeded()
            self.menuImgView.isUserInteractionEnabled = true
            self.menuImgView.isHidden = false
            
            if(isMoreDetailTapped){
                let fareBreakDownUv = GeneralFunctions.instantiateViewController(pageName: "FareBreakDownUV") as! FareBreakDownUV
                fareBreakDownUv.selectedCabTypeId = self.selectedCabTypeId
                fareBreakDownUv.pickUpLocation = self.pickUpLocation
                fareBreakDownUv.destLocation = self.destLocation
                fareBreakDownUv.promoCode = self.appliedPromoCode
                fareBreakDownUv.selectedCabTypeName = cabTypeItem.get("vVehicleType")
                fareBreakDownUv.isDestinationAdded = self.destAddress == "DEST_SKIPPED" ? "No" : "Yes"
                
                self.pushToNavController(uv: fareBreakDownUv)
            }
        }
        
        let fareDetailBGTapGue = UITapGestureRecognizer()
        fareDetailBGTapGue.addTarget(self, action: #selector(self.fareDetailBGViewTapped))
        fareDetailBGView.addGestureRecognizer(fareDetailBGTapGue)
        
        self.cntView.addSubview(fareDetailBGView)
        self.cntView.addSubview(fareDetailView)
        
        UIView.animate(withDuration: 0.5,
                       animations: {
                        self.fareDetailView.frame.origin.y =  self.cntView.frame.height - height
                        self.view.layoutIfNeeded()
        },  completion: { finished in
            self.fareDetailView.frame.origin.y =  self.cntView.frame.height - height
            self.view.layoutIfNeeded()
        })
        fareDetailView.cabTypeNameLbl.text = cabTypeItem.get("vVehicleType")
        
        fareDetailView.doneBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DONE"))
        
        let vLogo = cabTypeItem.get("vLogo1")
        
        var vCarLogoHoverImg = ""
        if(UIScreen.main.scale < 2){
            vCarLogoHoverImg = "1x_\(vLogo)"
        }else if(UIScreen.main.scale < 3){
            vCarLogoHoverImg = "2x_\(vLogo)"
        }else{
            vCarLogoHoverImg = "3x_\(vLogo)"
        }
        
        var hoverImgUrl = vVehicleImgPath + "\(selectedCabTypeId)/ios/\(vCarLogoHoverImg)"
        
        if(vLogo == ""){
            hoverImgUrl = "\(vVehicleDefaultImgPath)hover_ic_car.png"
        }
        
        fareDetailView.cabTypeImgView.sd_setImage(with: URL(string: hoverImgUrl), placeholderImage: UIImage(named: "placeHolder.png"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            //            GeneralFunctions.setImgTintColor(imgView: self.fareDetailView.cabTypeImgView, color: UIColor.UCAColor.AppThemeColor)
        })
        
        fareDetailView.capacityHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Capacity", key: "LBL_CAPACITY")
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased() || self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_Deliver.uppercased()){
            fareDetailView.capacityVLbl.text = "--"
        }else{
            fareDetailView.capacityVLbl.text = Configurations.convertNumToAppLocal(numStr: cabTypeItem.get("iPersonSize") + " \(self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PEOPLE_TXT"))")
        }
        
        fareDetailView.fareHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Fare", key: "LBL_FARE_TXT")
        fareDetailView.noteLbl.text = detailTxt

        fareDetailView.noteLbl.numberOfLines = (Double(detailTxtHeight / 20).rounded() < Double(detailTxtHeight / 20)) ? Int(detailTxtHeight / 20) : Int(Double(detailTxtHeight / 20).rounded())
        //        fareDetailView.noteLbl.fitText()
        
        fareDetailView.moreDetailsLbl.text = self.generalFunc.getLanguageLabel(origValue: "More Info", key: "LBL_MORE_INFO")
        
        let cabTypeFare = getCabTypeFare(currentVehicleTypeId: selectedCabTypeId)
        
        //        if(self.destAddress == "DEST_SKIPPED"){
        //            fareDetailView.moreDetailsLbl.isHidden = true
        //        }
        
        fareDetailView.fareVLbl.text = Configurations.convertNumToAppLocal(numStr: cabTypeFare == "" ? "--" : cabTypeFare)
        
    }
    
    func fareDetailBGViewTapped(){
        
        self.menuImgView.isUserInteractionEnabled = true
        self.menuImgView.isHidden = false
        
        if(fareDetailView != nil){
            fareDetailView.frame.origin.y = Application.screenSize.height + fareDetailView.frame.height
            fareDetailView.removeFromSuperview()
        }
        
        if(fareDetailBGView != nil){
            fareDetailBGView.frame.origin.y = Application.screenSize.height + fareDetailView.frame.height
            fareDetailBGView.removeFromSuperview()
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, insetForSectionAtIndex section: Int) -> UIEdgeInsets {
        
        let screenWidth = Application.screenSize.width
        let totalCellWidth = (120 * cabTypesArr.count)
        
        let leftInset = (collectionView.frame.width - CGFloat(totalCellWidth + 0)) / 2;
        let rightInset = leftInset
        
        if(screenWidth < CGFloat(totalCellWidth)){
            return UIEdgeInsetsMake(0, 0, 0, 0)
        }else{
            return UIEdgeInsetsMake(0, leftInset, 0, rightInset)
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return cabTypesArr.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "CabTypeCVCell", for: indexPath) as! CabTypeCVCell
        
        let tempDict = cabTypesArr[indexPath.item]
        let iVehicleTypeId = tempDict.get("iVehicleTypeId")
        
        
        if(self.selectedCabTypeId == iVehicleTypeId){
            cell.cabTypeHoverImgView.isHidden = false
            cell.cabTypeImgView.isHidden = true
            cell.cabTypeNameLbl.textColor = UIColor.UCAColor.AppThemeColor
            cell.fareInfoImgView.isHidden = (self.destAddress == "DEST_SKIPPED" || self.cabTypesFareArr.count == 0) ? true : false
            cell.fareEstLbl.paddingRight = (self.destAddress == "DEST_SKIPPED" || self.cabTypesFareArr.count == 0) ? 0 : 18
        }else{
            cell.cabTypeHoverImgView.isHidden = true
            cell.cabTypeImgView.isHidden = false
            cell.cabTypeNameLbl.textColor = UIColor(hex: 0x161718)
            cell.fareInfoImgView.isHidden = true
            cell.fareEstLbl.paddingRight = 0
        }
        
        GeneralFunctions.setImgTintColor(imgView: cell.fareInfoImgView, color: UIColor.UCAColor.AppThemeColor)
        
        //        if(Configurations.isRTLMode()){
        //            var scalingTransform : CGAffineTransform!
        //            scalingTransform = CGAffineTransform(scaleX: -1, y: 1);
        //            cell.transform = scalingTransform
        //         }
        
        cell.fareEstLbl.text = Configurations.convertNumToAppLocal(numStr: getCabTypeFare(currentVehicleTypeId: iVehicleTypeId))
        
        cell.cabTypeNameLbl.text = tempDict.get("vVehicleType")
        
        Utils.createRoundedView(view: cell.cabTypeImgView, borderColor: UIColor(hex: 0xcbcbcb), borderWidth: 1)
        Utils.createRoundedView(view: cell.cabTypeHoverImgView, borderColor: UIColor.UCAColor.AppThemeColor, borderWidth: 1)
        
        var vCarLogoImg = ""
        var vCarLogoHoverImg = ""
        if(UIScreen.main.scale < 2){
            vCarLogoImg = "1x_\(tempDict.get("vLogo"))"
            vCarLogoHoverImg = "1x_\(tempDict.get("vLogo1"))"
        }else if(UIScreen.main.scale < 3){
            vCarLogoImg = "2x_\(tempDict.get("vLogo"))"
            vCarLogoHoverImg = "2x_\(tempDict.get("vLogo1"))"
        }else{
            vCarLogoImg = "3x_\(tempDict.get("vLogo"))"
            vCarLogoHoverImg = "3x_\(tempDict.get("vLogo1"))"
        }
        var imgUrl = vVehicleImgPath + "\(iVehicleTypeId)/ios/\(vCarLogoImg)"
        
        var hoverImgUrl = vVehicleImgPath + "\(iVehicleTypeId)/ios/\(vCarLogoHoverImg)"
        
        if(tempDict.get("vLogo") == ""){
            imgUrl = "\(vVehicleDefaultImgPath)ic_car.png"
        }
        if(tempDict.get("vLogo1") == ""){
            hoverImgUrl = "\(vVehicleDefaultImgPath)hover_ic_car.png"
        }
        
        self.setCabTypeImage(imgView: cell.cabTypeHoverImgView, tintImgColor: UIColor.UCAColor.AppThemeTxtColor, imgUrl: hoverImgUrl, defaultImgUrl: "\(self.vVehicleDefaultImgPath)hover_ic_car.png", isCheckAgain: true)
        
        self.setCabTypeImage(imgView: cell.cabTypeImgView, tintImgColor: UIColor(hex: 0x999fa2), imgUrl: imgUrl, defaultImgUrl: "\(vVehicleDefaultImgPath)ic_car.png", isCheckAgain: true)
        
        cell.cabTypeImgView.backgroundColor = UIColor(hex: 0xffffff)
        cell.cabTypeHoverImgView.backgroundColor = UIColor(hex: 0xffffff)
        
//        cell.cabTypeImgView.backgroundColor = UIColor(hex: 0xebebeb)
//        cell.cabTypeHoverImgView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        //        GeneralFunctions.setImgTintColor(imgView: cell.cabTypeHoverImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        //        GeneralFunctions.setImgTintColor(imgView: cell.cabTypeImgView, color: UIColor(hex: 0x999fa2))
        
        if(indexPath.item == 0){
            cell.leftSeperationTopView.isHidden = true
            cell.leftSeperationBottomView.isHidden = true
            
        }else{
            cell.leftSeperationTopView.isHidden = false
            cell.leftSeperationBottomView.isHidden = false
        }
        
        if(indexPath.item == (self.cabTypesArr.count - 1)){
            cell.rightSeperationTopView.isHidden = true
            cell.rightSeperationBottomView.isHidden = true
        }else{
            cell.rightSeperationTopView.isHidden = false
            cell.rightSeperationBottomView.isHidden = false
        }
        
        return cell
    }
    
    func setCabTypeImage(imgView:UIImageView, tintImgColor:UIColor, imgUrl:String, defaultImgUrl:String, isCheckAgain:Bool){
        imgView.sd_setImage(with: URL(string: imgUrl), placeholderImage: UIImage(named: "placeHolder.png"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            if(error != nil && isCheckAgain == true){
                self.setCabTypeImage(imgView: imgView, tintImgColor: tintImgColor, imgUrl: defaultImgUrl, defaultImgUrl: defaultImgUrl, isCheckAgain: false)
            }
            //            GeneralFunctions.setImgTintColor(imgView: imgView, color: tintImgColor)
        })
    }
    
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return 1
    }
    
    func updateDriverLocationBeforeTrip(iDriverId:String, latitude:String, longitude:String, dataDict:NSDictionary){
        let driverMarker_temp = getDriverMarkerOnPubNubMsg(iDriverId: iDriverId, isRemoveFromList: false)
        
        if(driverMarker_temp != nil){
            let currentLocation = CLLocation(latitude: driverMarker_temp!.position.latitude, longitude: driverMarker_temp!.position.longitude)
            let location = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: latitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: longitude))
            
            let rotationAngle = currentLocation.bearingToLocationDegrees(destinationLocation: location, currentRotation: driverMarker_temp!.rotation)
            
            
            //            driverMarker_temp!.rotation =
            //            driverMarker_temp!.position = location.coordinate
            
            
            //            if(rotationAngle != -1.000){
            if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                driverMarker_temp!.position = location.coordinate
            }else{
                Utils.updateMarker(marker: driverMarker_temp!, googleMap: self.gMapView, coordinates: location.coordinate, rotationAngle: rotationAngle, duration: 1.0)
            }
            
            //            }
        }
    }
    
    func updateDriverLocation(iDriverId:String, latitude:String, longitude:String, dataDict:NSDictionary){
        if(currentCabGeneralType == Utils.cabGeneralType_UberX){
            return
        }
        updateAssignedDriverMarker(driverLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: latitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: longitude)), dataDict: dataDict)
    }
    
    func setDriverArrivedStatus(){
        if(self.isDriverAssigned == false || self.isTripStarted == true || self.isTripFinished == true || self.gMapView == nil){
            self.releaseAllTask()
            let window = Application.window
            
            let getUserData = GetUserData(uv: self, window: window!)
            getUserData.getdata()
            return
        }
        
        
        //        if(self.isDriverArrived == false){
        //            self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Driver has been arrived to your location.", key: "LBL_DRIVER_ARRIVE"))
        //        }
        
        setNavBar(isHidden: false)
        setLblToTitleView()
        if(self.titleLbl != nil){
            self.titleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVED_TXT")
        }
        
        self.isDriverArrived = true
        
        if(self.updateDirection != nil){
            self.updateDirection.releaseTask()
        }
        
        if(waitingMarker != nil){
            waitingMarker.map = nil
        }
        
        getAllCenter()
    }
    
    func getAvailableDriverIds() -> String{
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            return self.ufxSelectedServiceProviderId
        }
        
        var driverIds = ""
        
        var finalLoadedDriverList = [NSDictionary]()
        finalLoadedDriverList.append(contentsOf: self.currentLoadedDriverList)
        
        if(userProfileJson.get("DRIVER_REQUEST_METHOD") == "Distance"){
            finalLoadedDriverList = (finalLoadedDriverList as NSArray).sortedArray(using: [NSSortDescriptor(key: "DIST_TO_PICKUP", ascending: true)]) as! [NSDictionary]
        }
        
        for i in 0..<finalLoadedDriverList.count{
            let iDriverId = finalLoadedDriverList[i].get("driver_id")
            driverIds = driverIds == "" ? iDriverId : (driverIds + "," + iDriverId)
        }
        return driverIds
    }
    
    func getAvailableCarTypesIds() -> String{
        var carTypesIds = ""
        
        var finalLoadedCarTypeIds = [NSDictionary]()
        finalLoadedCarTypeIds.append(contentsOf: self.cabTypesArr)
        
        for i in 0..<finalLoadedCarTypeIds.count{
            let iVehicleTypeId = finalLoadedCarTypeIds[i].get("iVehicleTypeId")
            
            carTypesIds = carTypesIds == "" ? iVehicleTypeId : (carTypesIds + "," + iVehicleTypeId)
        }
        
        return carTypesIds
    }
    
    func myBtnTapped(sender: MyButton) {
        if(self.requestNowBtn != nil && sender == self.requestNowBtn){
            if(currentLoadedDriverList.count == 0){
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "No cars available in selected vehicle type.", key: "LBL_NO_CARS_AVAIL_IN_TYPE"))
                return
            }
            
            if(isRouteDrawnFailed){
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DEST_ROUTE_NOT_FOUND"))
                return
            }
            
            if((self.userProfileJson.get("APP_PAYMENT_MODE") == "Card" && isCardValidated == false) || (isCashPayment == false && isCardValidated == false)){
                checkCardConfig(isRideLater: false, isAutoContinue: true)
                return
            }
            
            isDeliveryDataEntered = false
            
            checkSurgePrice(selectedTime: "", isRideLater: false, ufxSelectedDriverIndex: -1)
            
            
        }else if(retryReqBtn != nil && sender == retryReqBtn){
            self.startDriverRequestQueue()
        }else if(surgeAcceptBtn != nil  && sender == surgeAcceptBtn){
            
            
            if(isSurgeFromAddDestination){
                self.cancelSurgeView()
                self.addDestinationOnTrip(latitude: self.destinationOnTripLatitude, longitude: self.destinationOnTripLongitude, address: self.destinationOnTripAddress, eConfirmByUser: "Yes")
                return
            }
            
            self.cancelSurgeView()
            
            if(isRideLaterFromSurge == false){
                self.requestCab()
            }else{
                self.continueRideLaterSchedule()
            }
        }else if(sender.btnType == "UFX_DRIVER_MORE_INFO"){
            if(providerDetailMarkerView != nil){
                providerDetailMarkerView.closeViewTapped()
            }
            self.ufxSelectedServiceProviderId = ""
            let openProviderDetailView = OpenProviderDetailView(uv: self, containerView: self.contentView)
            openProviderDetailView.setViewHandler(handler: { (isContinueBtnTapped) in
                if(isContinueBtnTapped == true){
                    self.checkSurgePrice(selectedTime: "", isRideLater: false, ufxSelectedDriverIndex: sender.tag)
                }
            })
            
            openProviderDetailView.show(dataDict: currentLoadedDriverList[sender.tag], eUnit: userProfileJson.get("eUnit"))
        }else if(self.requestPickUpView != nil && sender == self.rideLaterBtn){
            self.continueRideLaterSchedule()
        }
    }
    
    func addDriverNotificationObserverForRideLater(){
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.driverCallBackReceivedRideLater(sender:)), name: NSNotification.Name(rawValue: Utils.driverCallBackNotificationKey), object: nil)
    }
    
    func addDriverNotificationObserver(){
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.driverCallBackReceived(sender:)), name: NSNotification.Name(rawValue: Utils.driverCallBackNotificationKey), object: nil)
    }
    
    func driverCallBackReceivedRideLater(sender: NSNotification){
        let userInfo = sender.userInfo
        let msgData = (userInfo!["body"] as! String).getJsonDataDict()
        
        let msgStr = msgData.get("Message")
        
        if(msgStr == "CabRequestAccepted"){
            if(msgData.get("iCabBookingId") != "" && msgData.get("iCabBookingId") != "0"){
                self.releaseAllTask()
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                
                return
            }
        }
    }
    
    func driverCallBackReceived(sender: NSNotification){
        let userInfo = sender.userInfo
        let msgData = (userInfo!["body"] as! String).getJsonDataDict()
        
        let msgStr = msgData.get("Message")
        
        Utils.closeKeyboard(uv: self)
        
        if(self.reqSentErrorDialog != nil){
            self.reqSentErrorDialog.disappear()
            self.reqSentErrorDialog = nil
        }
        
        if(msgStr == "CabRequestAccepted"){
            if(self.isDriverAssigned == true){
                return
            }
            
            //            LocalNotification.dispatchlocalNotification(with: "", body: msgData.get("vTitle"), at: Date().addedBy(seconds: 0), onlyInBackground: true)
            
            self.stopDriverRequestQueue()
            self.isDriverAssigned = true
            assignedDriverId = msgData.get("iDriverId")
            assignedTripId = msgData.get("iTripId")
            
            if(msgData.get("iCabBookingId") != "" && msgData.get("iCabBookingId") != "0"){
                self.releaseAllTask()
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                
                return
            }
            
            if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                self.ufxDriverAcceptedReqNow = true
                loadBookingFinishView()
                return
            }
            
            setNavBar(isHidden: false)
            setLblToTitleView()
            self.titleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ARRIVING_TXT")
            
            configureAssignedDriver(isAppRestarted:false)
            
            self.sourcePickUpEtaLbl.isHidden = true
            
        }else if(msgStr == "TripStarted"){
            if(self.isDriverAssigned == false){
                self.releaseAllTask()
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                return
            }
            
            if(self.isTripStarted == true){
                return
            }
            //            LocalNotification.dispatchlocalNotification(with: "", body: msgData.get("vTitle"), at: Date().addedBy(seconds: 0), onlyInBackground: true)
            
            setTripStartData(deliveryConfirmCode: msgData.get("VerificationCode"))
        }else if(msgStr == "DestinationAdded"){
            
            
            var contentMsg = ""
            if(msgData.get("vTitle") == ""){
                contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DEST_ADD_BY_DRIVER")
            }else{
                contentMsg = msgData.get("vTitle")
            }
            
            self.generalFunc.setAlertMessage(uv: self, title: "", content: contentMsg, positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                
                self.setUpDestination()
                
            })
        }else if(msgStr == "TripCancelledByDriver" || msgStr == "TripEnd"){
            if(self.isDriverAssigned == false || (msgStr == "TripEnd" && self.isTripStarted == false)){
                self.releaseAllTask()
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                return
            }
            
            if(self.isTripFinished == true){
                return
            }
            
            var contentMsg = ""
            if(msgData.get("vTitle") == ""){
                
                if(msgStr == "TripCancelledByDriver"){
                    contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " " + msgData.get("Reason") + " " + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP_BY_DRIVER_MSG_SUFFIX")
                }else{
                    contentMsg = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_END_TRIP_DIALOG_TXT")
                }
            }else{
                contentMsg = msgData.get("vTitle")
            }
            
            self.isTripFinished = true
            
            self.generalFunc.setAlertMessage(uv: self, title: "", content: contentMsg, positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                
                self.releaseAllTask()
                
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                
            })
        }
    }
    
    
    
    func configureAssignedDriver(isAppRestarted:Bool){
        
        closeCabReqView()
        removeAddReqPickUpView(isDriverAssigned: true)
        
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_REC_NAME_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_REC_MOB_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_PICKUP_INS_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_DELIVERY_INS_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_PACKAGE_TYPE_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_PACKAGE_TYPE_ID_KEY)
        GeneralFunctions.removeValue(key: Utils.DELIVERY_DETAILS_PACKAGE_DETAILS_KEY)
        
        if(self.recentLocView != nil){
            self.recentLocView.isViewHidden = true
            self.recentLocView.isHidden = true
            if(self.recentLocView.view != nil){
                self.recentLocView.view.removeFromSuperview()
                self.recentLocView.view.isHidden = true
            }
        }
        
        if(self.loadAvailableCab != nil){
            self.loadAvailableCab.onPauseCalled()
            self.loadAvailableCab.setTaskKilledValue(isTaskKilled: true)
            self.loadAvailableCab = nil
        }
        self.isMyLocationEnabled = false
        self.gMapView.isMyLocationEnabled = isMyLocationEnabled
        self.gMapView.clear()
        
        drvierDetailViewHeight = drvierDetailViewHeight + GeneralFunctions.getSafeAreaInsets().bottom
        
        if(Configurations.isIponeXDevice()){
            drvierDetailViewHeight = drvierDetailViewHeight - 20
        }
        
        if(sourcePinImgView.isHidden == true){
            self.sourcePickUpEtaLbl.isHidden = true
        }
        
        driverDetailView = self.generalFunc.loadView(nibName: "DriverDetailView", uv: self, isWithOutSize: true)
        driverDetailView.frame = CGRect(x: 0, y: self.cntView.frame.height, width: Application.screenSize.width, height: drvierDetailViewHeight)
        
        self.cntView.addSubview(driverDetailView)
        
        
        if(isAppRestarted == true){
            addDriverNotificationObserver()
            
            self.isDriverAssigned = true
            
            let tripDetailJson = userProfileJson.getObj("TripDetails")
            let driverDetailJson = userProfileJson.getObj("DriverDetails")
            let driverCarDetailJson = userProfileJson.getObj("DriverCarDetails")
            
            let vTripPaymentMode = tripDetailJson.get("vTripPaymentMode")
            let tEndLat = tripDetailJson.get("tEndLat")
            let tEndLong = tripDetailJson.get("tEndLong")
            let tDaddress = tripDetailJson.get("tDaddress")
            
            let driverTripStatus = driverDetailJson.get("vTripStatus")
            
           
            
            if(driverTripStatus != "Active"){
                setNavBar(isHidden: false)
                setLblToTitleView()
                self.titleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVED_TXT")
                
                self.isDriverArrived = true
            }
            
            if (vTripPaymentMode == "Cash") {
                self.isCashPayment = true
            } else {
                self.isCashPayment = false
            }
            assignedDriverId = tripDetailJson.get("iDriverId")
            assignedTripId = tripDetailJson.get("iTripId")
            eTripType = tripDetailJson.get("eType")
            
            self.selectedCabTypeId = tripDetailJson.get("iVehicleTypeId")
            
            
            addressContainerView.pickUpAddressLbl.text = tripDetailJson.get("tSaddress")
            
            if (tEndLat != "0.0" && tEndLong != "0.0" && tDaddress != "Not Set" && tEndLat != "" && tEndLong != "" && tDaddress != "") {
                destAddress = tDaddress
                self.destLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tEndLat), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tEndLong))
                
                addressContainerView.destAddressLbl.text = tDaddress
            }else{
                destAddress = ""
                addressContainerView.destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
            }
            
            
            self.pickUpLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripDetailJson.get("tStartLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripDetailJson.get("tStartLong")))
            
            self.destLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tEndLat), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tEndLong))
            
            assignedDriverData["PickUpLatitude"] = "\(tripDetailJson.get("tStartLat"))"
            assignedDriverData["PickUpLongitude"] = "\(tripDetailJson.get("tStartLong"))"
            assignedDriverData["PickUpAddress"] = "\(tripDetailJson.get("tSaddress"))"
            assignedDriverData["vVehicleType"] = "\(tripDetailJson.get("vVehicleType"))"
            assignedDriverData["vDeliveryConfirmCode"] = "\(tripDetailJson.get("vDeliveryConfirmCode"))"
            assignedDriverData["eType"] = "\(tripDetailJson.get("eType"))"
            assignedDriverData["TripStatus"] = "\(driverDetailJson.get("vTripStatus"))"
            assignedDriverData["DriverTripStatus"] = "\(driverDetailJson.get("vTripStatus"))"
            assignedDriverData["DriverPhone"] = "\(driverDetailJson.get("vPhone"))"
            assignedDriverData["DriverRating"] = "\(driverDetailJson.get("vAvgRating"))"
            assignedDriverData["DriverAppVersion"] = "\(driverDetailJson.get("iAppVersion"))"
            assignedDriverData["DriverLatitude"] = "\(driverDetailJson.get("vLatitude"))"
            assignedDriverData["DriverLongitude"] = "\(driverDetailJson.get("vLongitude"))"
            assignedDriverData["DriverImage"] = "\(driverDetailJson.get("vImage"))"
            assignedDriverData["DriverName"] = "\(driverDetailJson.get("vName"))"
            assignedDriverData["DriverCarPlateNum"] = "\(driverCarDetailJson.get("vLicencePlate"))"
            assignedDriverData["DriverCarColor"] = "\(driverCarDetailJson.get("vColour"))"
            assignedDriverData["DriverCarName"] = "\(driverCarDetailJson.get("make_title"))"
            assignedDriverData["DriverCarModelName"] = "\(driverCarDetailJson.get("model_title"))"
            assignedDriverData["vDeliveryConfirmCode"] = "\(tripDetailJson.get("vDeliveryConfirmCode"))"
            assignedDriverData["eTollSkipped"] = "No"
            assignedDriverData["eIconType"] = "\(tripDetailJson.get("eIconType"))"
            
        }else{
            if(self.currentLoadedDriverList.count == 0){
                self.releaseAllTask()
                let window = Application.window
                
                let getUserData = GetUserData(uv: self, window: window!)
                getUserData.getdata()
                return
            }
            
            eTripType = self.currentCabGeneralType
            
            var isDriverIdMatch = false
            
            for i in 0..<currentLoadedDriverList.count{
                let driverDataMap = currentLoadedDriverList[i]
                
                let iDriverId = driverDataMap.get("driver_id")
                
                if(iDriverId == self.assignedDriverId){
                    
                    isDriverIdMatch = true
                    
                    assignedDriverData["PickUpLatitude"] = "\(self.pickUpLocation.coordinate.latitude)"
                    assignedDriverData["PickUpLongitude"] = "\(self.pickUpLocation.coordinate.longitude)"
                    assignedDriverData["PickUpAddress"] = "\(self.pickUpAddress)"
                    assignedDriverData["vVehicleType"] = "\(GeneralFunctions.getSelectedCarTypeData(selectedCarTypeId: selectedCabTypeId, dataKey: "vVehicleType", carTypesArr: cabTypesArr as NSArray))"
                    assignedDriverData["vDeliveryConfirmCode"] = ""
                    assignedDriverData["DriverTripStatus"] = ""
                    assignedDriverData["TripStatus"] = ""
                    assignedDriverData["DriverPhone"] = "\(driverDataMap.get("vPhone_driver"))"
                    assignedDriverData["DriverRating"] = "\(driverDataMap.get("average_rating"))"
                    assignedDriverData["DriverAppVersion"] = "\(driverDataMap.get("iAppVersion"))"
                    assignedDriverData["DriverLatitude"] = "\(driverDataMap.get("Latitude"))"
                    assignedDriverData["DriverLongitude"] = "\(driverDataMap.get("Longitude"))"
                    assignedDriverData["DriverImage"] = "\(driverDataMap.get("driver_img"))"
                    assignedDriverData["DriverName"] = "\(driverDataMap.get("Name"))"
                    assignedDriverData["DriverCarPlateNum"] = "\(driverDataMap.get("vLicencePlate"))"
                    assignedDriverData["DriverCarColor"] = "\(driverDataMap.get("vColour"))"
                    assignedDriverData["DriverCarName"] = "\(driverDataMap.get("make_title"))"
                    assignedDriverData["DriverCarModelName"] = "\(driverDataMap.get("model_title"))"
                    assignedDriverData["eType"] = "\(self.currentCabGeneralType)"
                    assignedDriverData["vDeliveryConfirmCode"] = ""
                    assignedDriverData["eTollSkipped"] = "No"
                    assignedDriverData["eIconType"] = "\(driverDataMap.get("eIconType"))"
                    
                    break
                }
                
            }
            
            if(destAddress == "DEST_SKIPPED"){
                destAddress = ""
                addressContainerView.destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
            }
        }
        
        assignedDriverData["iDriverId"] = "\(self.assignedDriverId)"
        assignedDriverData["iTripId"] = "\(self.assignedTripId)"
        assignedDriverData["PassengerName"] = "\(self.userProfileJson.get("vName"))"
        assignedDriverData["PassengerImageName"] = "\(self.userProfileJson.get("vImgName"))"
        
        setDriverData()
        
        if(GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") != nil && (GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") as! String) == "true"){
            let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
            
            let assignedDriverData = self.assignedDriverData as NSDictionary
            
            GeneralFunctions.removeValue(key: "OPEN_MSG_SCREEN")
            
            chatUV.receiverId = assignedDriverData.get("iDriverId")
            chatUV.receiverDisplayName = assignedDriverData.get("DriverName")
            chatUV.assignedtripId = assignedDriverData.get("iTripId")
            chatUV.pPicName = assignedDriverData.get("DriverImage")
            
            self.pushToNavController(uv:chatUV, isDirect: true)
            
        }
        
        
        if(isTripStarted == false){
            setDriverDetailViewHeight()
        }else{
            if(GeneralFunctions.getValue(key: "IS_AUTO_FOCUS_TO_DEST") != nil && (GeneralFunctions.getValue(key: "IS_AUTO_FOCUS_TO_DEST") as! String) == "Yes"){
                GeneralFunctions.saveValue(key: "IS_AUTO_FOCUS_TO_DEST", value: "No" as AnyObject)
                self.addressContainerView.destViewTapped(isAutoOpenSelection: false)
            }

        }
    }
    
    func setDriverDetailViewHeight(){
        self.driverDetailView.frame.size.height = self.drvierDetailViewHeight
        
        if(self.gMapView == nil){
            self.releaseAllTask()
            
            let window = Application.window
            
            let getUserData = GetUserData(uv: self, window: window!)
            getUserData.getdata()
            return
        }
        
        UIView.animate(withDuration: 0.8,
                       animations: {
                        self.myLocBottomMargin.constant = self.drvierDetailViewHeight + 10
                        self.driverDetailView.frame.origin.y = self.cntView.frame.height - self.drvierDetailViewHeight
                        if(self.gMapView != nil){
                            self.gMapView.frame.size.height = self.cntView.frame.size.height - self.drvierDetailViewHeight
                            self.view.layoutIfNeeded()
                        }
        },  completion: { finished in
            self.myLocBottomMargin.constant = self.drvierDetailViewHeight + 10
            self.driverDetailView.frame.origin.y = self.cntView.frame.height - self.drvierDetailViewHeight
            if(self.gMapView != nil){
                self.gMapView.frame.size.height = self.cntView.frame.size.height - self.drvierDetailViewHeight
                self.view.layoutIfNeeded()
                
                
                if(GeneralFunctions.getValue(key: "IS_AUTO_FOCUS_TO_DEST") != nil && (GeneralFunctions.getValue(key: "IS_AUTO_FOCUS_TO_DEST") as! String) == "Yes"){
                    GeneralFunctions.saveValue(key: "IS_AUTO_FOCUS_TO_DEST", value: "No" as AnyObject)
                    self.addressContainerView.destViewTapped(isAutoOpenSelection: false)
                }else{
                    
                    self.getAllCenter()
                }

            }
            
            
        })
    }
    
    func setDriverData(){
        
        Utils.driverMarkersPositionList.removeAll()
        Utils.driverMarkerAnimFinished = true
        
        if(configPubNub != nil){
            configPubNub!.iTripId = self.assignedTripId
        }
        
        if(sourcePinImgView.isHidden == true){
            self.sourcePickUpEtaLbl.isHidden = true
        }
        
        GeneralFunctions.saveValue(key: "isDriverAssigned", value: "true" as AnyObject)
        
        let assignedDriverData = self.assignedDriverData as NSDictionary
        
        let driverLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: assignedDriverData.get("DriverLatitude")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: assignedDriverData.get("DriverLongitude")))
        
        updateAssignedDriverMarker(driverLocation: driverLocation, dataDict: nil)
        
        if(updateDirection != nil){
            self.updateDirection.releaseTask()
        }
        
        if(assignedDriverData.get("TripStatus") == "On Going Trip"){
            setTripStartData(deliveryConfirmCode: assignedDriverData.get("vDeliveryConfirmCode"))
            //            setTripStartData()
        }else{
            
            if(assignedDriverData.get("TripStatus") != "Arrived"){
                
                self.updateDirection = UpdateDirections(uv: self, gMap: self.gMapView, fromLocation: driverLocation, destinationLocation: self.pickUpLocation, isCurrentLocationEnabled: false)
                self.updateDirection.onDirectionUpdateDelegate = self
                self.updateDirection.scheduleDirectionUpdate()
                
                addWaitingMarker(fromLocation: driverLocation, toLocation: self.pickUpLocation, waitingTime: "")
            }
        }
        
        if(self.configPubNub == nil){
            let DRIVER_LOC_FETCH_TIME_INTERVAL = GeneralFunctions.parseDouble(origValue: 5, data: self.userProfileJson.get("DRIVER_LOC_FETCH_TIME_INTERVAL"))
            updateFreqDriverLocTask = UpdateFreqTask(interval: DRIVER_LOC_FETCH_TIME_INTERVAL)
            updateFreqDriverLocTask.currInst = updateFreqDriverLocTask
            updateFreqDriverLocTask.setTaskRunListener(onTaskRunCalled: self)
        }
        
        self.driverCallLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CALL_TXT")
        self.driverMsgLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MESSAGE_TXT")
        self.driverShareLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SHARE_BTN_TXT")
        self.driverCancelTripLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT")
        
        self.driverNameLbl.text = assignedDriverData.get("DriverName")
        self.driverCarType.text = "(\(assignedDriverData.get("DriverCarColor") == "" ? assignedDriverData.get("vVehicleType") : assignedDriverData.get("DriverCarColor")))"
        self.driverCardNameLbl.text = assignedDriverData.get("DriverCarName") + "\n" + assignedDriverData.get("DriverCarModelName")
        self.driverCarPlateNoLbl.text = assignedDriverData.get("DriverCarPlateNum")
        
        driverPicImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + "\(assignedDriverData.get("iDriverId"))/\(assignedDriverData.get("DriverImage"))"), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            
        })
        
        Utils.createRoundedView(view: driverPicImgView, borderColor: UIColor.clear, borderWidth: 0)
        
        self.carIcBgView.backgroundColor = UIColor.UCAColor.AppThemeColor
        GeneralFunctions.setImgTintColor(imgView: self.carIcImgView, color: UIColor.UCAColor.AppThemeTxtColor)
        
        GeneralFunctions.setImgTintColor(imgView: self.driverCallImgView, color: UIColor.UCAColor.AppThemeColor)
        GeneralFunctions.setImgTintColor(imgView: self.driverMsgImgView, color: UIColor.UCAColor.AppThemeColor)
        GeneralFunctions.setImgTintColor(imgView: self.driverCancelTripImgView, color: UIColor.UCAColor.AppThemeColor)
        GeneralFunctions.setImgTintColor(imgView: self.driverShareImgView, color: UIColor.UCAColor.AppThemeColor)
        
        let cancelTripTapGue = UITapGestureRecognizer()
        cancelTripTapGue.addTarget(self, action: #selector(self.cancelTripTapped))
        
        let callDriverTapGue = UITapGestureRecognizer()
        callDriverTapGue.addTarget(self, action: #selector(self.callDriverTapped))
        
        let msgDriverTapGue = UITapGestureRecognizer()
        msgDriverTapGue.addTarget(self, action: #selector(self.msgDriverTapped))
        
        let shareTripTapGue = UITapGestureRecognizer()
        shareTripTapGue.addTarget(self, action: #selector(self.shareTripTapped))
        
        self.driverCancelTripView.isUserInteractionEnabled = true
        self.driverCallView.isUserInteractionEnabled = true
        self.driverMsgView.isUserInteractionEnabled = true
        self.driverShareView.isUserInteractionEnabled = true
        
        self.driverCancelTripView.addGestureRecognizer(cancelTripTapGue)
        self.driverCallView.addGestureRecognizer(callDriverTapGue)
        self.driverMsgView.addGestureRecognizer(msgDriverTapGue)
        self.driverShareView.addGestureRecognizer(shareTripTapGue)
        
        self.driverRatingLbl.text = Configurations.convertNumToAppLocal(numStr: assignedDriverData.get("DriverRating"))
        self.driverRatingBar.rating = GeneralFunctions.parseFloat(origValue: 0, data: assignedDriverData.get("DriverRating"))
//        self.driverRatingBar.rating = 4.7

        subscribeToDriverLocChannel()
        
        if(self.addressContainerView != nil){
            self.addressContainerView.isDriverAssigned = self.isDriverAssigned
            self.addressContainerView.addDestImgView.isHidden = true
            self.addressContainerView.addPickUpImgView.isHidden = true
            
            addressContainerView.setLocationIndicator()

        }
        
        //        self.getAllCenter()
    }
    
    func callDriverTapped(){
        let assignedDriverData = self.assignedDriverData as NSDictionary

        let number = "\(assignedDriverData.get("DriverPhone"))"
        UIApplication.shared.openURL(NSURL(string:"telprompt:" + number)! as URL)
    }
    
    
    
    func msgDriverTapped(){
        let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
        chatUV.receiverId = assignedDriverData["iDriverId"]!
        chatUV.receiverDisplayName = assignedDriverData["DriverName"]!
        chatUV.assignedtripId = assignedDriverData["iTripId"]!
        chatUV.pPicName = assignedDriverData["DriverImage"]!
        //        self.pushToNavController(uv:chatUV)
        
        self.pushToNavController(uv:chatUV, isDirect: true)
        
        //        let assignedDriverData = self.assignedDriverData as NSDictionary
        //
        //        let number = "\(assignedDriverData.get("DriverPhone"))"
        //        UIApplication.shared.openURL(NSURL(string:"sms:" + number)! as URL)
    }
    
    func cancelTripTapped(){
        
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_CANCEL_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TRIP_NOW"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTINUE_TRIP_TXT"), completionHandler: { (btnClickedIndex) in
            
            if(btnClickedIndex == 0){
                self.continueCancelTrip()
            }
        })
        
        
    }
    
    func continueCancelTrip(){
        
        let parameters = ["type":"cancelTrip", "iUserId": GeneralFunctions.getMemberd(), "iDriverId": self.assignedDriverId, "UserType": Utils.appUserType, "iTripId": self.assignedTripId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Your trip is successfully canceled.", key: "LBL_SUCCESS_TRIP_CANCELED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                        
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                    })
                }else{
                    
                    if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                        
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                        return
                    }
                    
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    func shareTripTapped(){
        
//        let latitude = "\(assignedDriverLocation.coordinate.latitude)"
//        let longitude = "\(assignedDriverLocation.coordinate.longitude)"
        
        let getAddrFrmLocation = GetAddressFromLocation(uv: self)
        getAddrFrmLocation.setLocation(latitude: assignedDriverLocation.coordinate.latitude, longitude: assignedDriverLocation.coordinate.longitude)
        
        getAddrFrmLocation.setHandler { (address, location, isPickUpMode, dataResult) in
            let map_location_url = "http://maps.google.com/?q=" + address.addingPercentEncoding(withAllowedCharacters: .urlFragmentAllowed)!
            
            let share_txt_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SEND_STATUS_CONTENT_TXT") + " " + map_location_url
            
            let objectsToShare = [share_txt_str]
            let activityVC = UIActivityViewController(activityItems: objectsToShare, applicationActivities: nil)
            activityVC.excludedActivityTypes = [UIActivityType.airDrop, UIActivityType.addToReadingList]
            activityVC.popoverPresentationController?.sourceView = self.view
            self.present(activityVC, animated: true, completion: nil)
        }
        getAddrFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: true)

    }
    
    func onTaskRun(currInst: UpdateFreqTask) {
        if(currInst == self.animTask){
            
            if (self.i_anim < self.animLocationArr.count) {
                
                self.animationPath.add(self.animLocationArr[i_anim])
                self.animationPolyline.path = self.animationPath
                
                self.animationPolyline.strokeColor = UIColor.gray
                self.animationPolyline.strokeWidth = 5
                
                self.animationPolyline.map = self.gMapView
                
                self.i_anim += 1
            }
            else {
                self.i_anim = 0
                self.animationPath = GMSMutablePath()
                self.animationPolyline.map = nil
            }
            
            return
        }
        checkDriverLocation()
    }
    
    func checkDriverLocation(){
        
        let parameters = ["type":"getDriverLocations", "iUserId": GeneralFunctions.getMemberd(), "iDriverId": self.assignedDriverId, "UserType": Utils.appUserType]
        
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
                    if(vLatitude != "" && vLatitude != "0.0" && vLatitude != "-180.0" && vLongitude != "" && vLongitude != "0.0" && vLongitude != "-180.0"){
                        
                        self.updateAssignedDriverMarker(driverLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: vLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: vLongitude)), dataDict: nil)
                    }
                }else{
                    //                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    
    func updateAssignedDriverMarker(driverLocation:CLLocation, dataDict:NSDictionary?){
        
        if(self.isDriverAssigned == false){
            return
        }
        
        self.assignedDriverLocation = driverLocation
        
        if(self.assignedDriverMarker == nil){
            let driverMarker = GMSMarker()
            self.assignedDriverMarker = driverMarker
        }
        
        if(self.updateDirection != nil){
            if(self.isTripStarted == false && self.isDriverAssigned == true){
                self.updateDirection.changeLocation(fromLocation: driverLocation, destinationLocation: self.pickUpLocation)
            }else if(self.isTripStarted == true && self.isDriverAssigned == true){
                self.updateDirection.changeLocation(fromLocation: self.assignedDriverLocation, destinationLocation: self.destLocation)
            }
        }
        
        
        var rotationAngle:Double = -1
        if(assignedDriverRotatedLocation != nil){
            rotationAngle = assignedDriverRotatedLocation.bearingToLocationDegrees(destinationLocation: driverLocation, currentRotation: assignedDriverMarker.rotation)
            if(rotationAngle != -1){
                assignedDriverRotatedLocation = driverLocation
            }
        }else{
            assignedDriverRotatedLocation = driverLocation
        }
        
        
        if(dataDict != nil && self.assignedDriverMarker != nil){
            
            let previousItemOfMarker = Utils.getLastLocationDataOfMarker(marker: assignedDriverMarker)
            
            var tempData = [String:String]()
            tempData["vLatitude"] = "\(driverLocation.coordinate.latitude)"
            tempData["vLongitude"] = "\(driverLocation.coordinate.longitude)"
            tempData["iDriverId"] = "\(self.assignedDriverId)"
            tempData["RotationAngle"] = "\(rotationAngle)"
            tempData["LocTime"] = "\(dataDict!.get("LocTime"))"
            
            if(previousItemOfMarker.get("LocTime") != "" && dataDict!.get("LocTime") != ""){
                
                let locTime = Int64(previousItemOfMarker.get("LocTime"))
                let newLocTime = Int64(dataDict!.get("LocTime"))
                
                if(locTime != nil && newLocTime != nil){
                    
                    if((newLocTime! - locTime!) > 0 && Utils.driverMarkerAnimFinished == false){
                        Utils.driverMarkersPositionList.append(tempData as NSDictionary)
                    }else if((newLocTime! - locTime!) > 0){
                        Utils.updateMarkerOnTrip(marker: assignedDriverMarker, googleMap: self.gMapView, coordinates: driverLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: self.assignedDriverId, LocTime: dataDict!.get("LocTime"))
                    }
                    
                }else if((locTime == nil || newLocTime == nil) && Utils.driverMarkerAnimFinished == false){
                    Utils.driverMarkersPositionList.append(tempData as NSDictionary)
                }else{
                        Utils.updateMarkerOnTrip(marker: assignedDriverMarker, googleMap: self.gMapView, coordinates: driverLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: self.assignedDriverId, LocTime: dataDict!.get("LocTime"))
                    
                }
                
            }else if(Utils.driverMarkerAnimFinished == false){
                Utils.driverMarkersPositionList.append(tempData as NSDictionary)
            }else{
                    Utils.updateMarkerOnTrip(marker: assignedDriverMarker, googleMap: self.gMapView, coordinates: driverLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: self.assignedDriverId, LocTime: dataDict!.get("LocTime"))
                
            }
            
        }else{
            
                Utils.updateMarkerOnTrip(marker: assignedDriverMarker, googleMap: self.gMapView, coordinates: driverLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: self.assignedDriverId, LocTime: "")
            
        }
        
        
        assignedDriverMarker.title = self.assignedDriverId
        //        assignedDriverMarker.icon = UIImage(named: "ic_driver_car_pin")
        
        let eIconType = assignedDriverData["eIconType"]
        
        var iconId = "ic_driver_car_pin"
        
        if(eIconType == "Bike"){
            iconId = "ic_bike"
        }else if(eIconType == "Cycle"){
            iconId = "ic_cycle"
        }else if(eIconType == "Truck"){
            iconId = "ic_truck"
        }
        
        assignedDriverMarker.icon = UIImage(named: iconId)
        assignedDriverMarker.map = self.gMapView
        assignedDriverMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        assignedDriverMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        assignedDriverMarker.isFlat = true
        
        
        if(isMapMoveToDriverLoc == false){
            
            let camera = GMSCameraPosition.camera(withLatitude: self.assignedDriverLocation!.coordinate.latitude,
                                                  longitude: self.assignedDriverLocation!.coordinate.longitude, zoom: self.gMapView.camera.zoom)
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            
            isMapMoveToDriverLoc = true
        }
        
    }
    
    
    func onDirectionUpdate(directionResultDict: NSDictionary) {
        let routesArr = directionResultDict.getArrObj("routes")
        let legs_arr = (routesArr.object(at: 0) as! NSDictionary).getArrObj("legs")
        let duration = (legs_arr.object(at: 0) as! NSDictionary).getObj("duration").get("text")
        let value = GeneralFunctions.parseDouble(origValue: 1.0, data: (legs_arr.object(at: 0) as! NSDictionary).getObj("duration").get("value"))
        
        
        if(self.isDriverAssigned == true && self.isTripStarted == true){
            if(self.destLocation != nil){
                
                addWaitingMarker(fromLocation: self.assignedDriverLocation, toLocation: self.destLocation, waitingTime: "\(value)")
            }
        }else if(self.isDriverAssigned == true ){
            if(assignedDriverLocation != nil && self.pickUpLocation != nil){
                addWaitingMarker(fromLocation: self.assignedDriverLocation, toLocation: self.pickUpLocation, waitingTime: "\(value)")
            }
            
            if(lastArrivingNotificationTime != nil && (Utils.currentTimeMillis() - lastArrivingNotificationTime) > 59000){
                return
            }
            if(value == 0.0 && arrivingNotificationCounter1 == false)
            {
                setArrivingNotification()
                arrivingNotificationCounter1 = true
            }
            else if(value <= 3.0 && arrivingNotificationCounter2 == false)
            {
                setArrivingNotification()
                arrivingNotificationCounter2 = true
            }
            else if(value <= 1.0 && arrivingNotificationCounter3 == false)
            {
                setArrivingNotification()
                arrivingNotificationCounter3 = true
            }
            
            
        }
        
    }
    func setArrivingNotification()
    {
        let localNotification = UILocalNotification()
        localNotification.fireDate =  NSDate(timeIntervalSinceNow: 2) as Date
        localNotification.alertBody = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DRIVER_ARRIVING_TXT")
        
        //localNotification.timeZone = NSTimeZone.init(name: "GMT") as TimeZone?
        
        localNotification.soundName = UILocalNotificationDefaultSoundName
        UIApplication.shared.scheduleLocalNotification(localNotification)
        
        lastArrivingNotificationTime = Utils.currentTimeMillis()
    }
    
    
    func addWaitingMarker(fromLocation:CLLocation, toLocation:CLLocation, waitingTime:String){
        
        if(waitingMarker == nil){
            waitingMarker = GMSMarker()
        }
        
        var minTime = "--"
        if(waitingTime != ""){
            
            minTime = Utils.formateSecondsToHours(seconds: waitingTime)
            
            let iconOfWaitingMarker = Utils.textToImage(drawText: minTime.replace(" ", withString: "\n") as NSString, inImage: UIImage(named:"ic_wait_marker")!, atYPoint: 12).resize(toHeight: 80)!
            heightOfWaitingMarker = iconOfWaitingMarker.size.height
            self.waitingMarker.icon = iconOfWaitingMarker
            
        }else{
            var DRIVER_ARRIVED_MIN_TIME_PER_MINUTE:Double = 3
            DRIVER_ARRIVED_MIN_TIME_PER_MINUTE = GeneralFunctions.parseDouble(origValue: 3, data: userProfileJson.get("DRIVER_ARRIVED_MIN_TIME_PER_MINUTE"))
            
            var distance = fromLocation.distance(from: toLocation) / 1000
            
            if(fromLocation.coordinate.latitude == 0.0 || fromLocation.coordinate.longitude == 0.0 || toLocation.coordinate.latitude == 0.0 || toLocation.coordinate.longitude == 0.0){
                distance = 0
            }
            
            let lowestTime = distance * DRIVER_ARRIVED_MIN_TIME_PER_MINUTE
            
            let lowestTime_int = Int(lowestTime)
            
            if(lowestTime_int < 1){
                minTime = "--"
            }else{
                minTime = "\(lowestTime_int)"
            }
            
            let iconOfWaitingMarker = Utils.textToImage(drawText: minTime + "\n" + self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MIN_SMALL_TXT") as NSString, inImage: UIImage(named:"ic_wait_marker")!, atYPoint: 8).resize(toHeight: 80)!
            
            heightOfWaitingMarker = iconOfWaitingMarker.size.height
            self.waitingMarker.icon = iconOfWaitingMarker
        }
        
        self.waitingMarker.position = toLocation.coordinate
        
        self.waitingMarker.map = self.gMapView
        self.waitingMarker.infoWindowAnchor = CGPoint(x: 0.5, y:0.5)
        self.waitingMarker.groundAnchor = CGPoint(x: 0.5, y: 1.0)
        
        //        if(waitingMarker.position.latitude == self.pickUpLocation.coordinate.latitude && waitingMarker.position.longitude == self.pickUpLocation.coordinate.longitude){
        //
        //        }else{
        //
        //        }
    }
    
    func subscribeToDriverLocChannel(){
        var channels =  [String]()
        channels += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER+self.assignedDriverId]
        if(configPubNub != nil){
            self.configPubNub?.subscribeToChannels(channels: channels)
        }
    }
    
    func unSubscribeToDriverLocChannel(){
        var channels =  [String]()
        channels += [Utils.PUBNUB_UPDATE_LOC_CHANNEL_PREFIX_DRIVER+self.assignedDriverId]
        if(configPubNub != nil){
            self.configPubNub?.subscribeToChannels(channels: channels)
        }
    }
    
    func setTripStartData(deliveryConfirmCode:String){
        self.isTripStarted = true
        self.myLocBottomMargin.constant = self.driverDetailView.frame.size.height + 15
        
        if(waitingMarker != nil){
            waitingMarker.map = nil
        }
        
        
        if(self.updateDirection != nil){
            self.updateDirection.releaseTask()
        }
        
        if(self.destLocation != nil){
            self.updateDirection = UpdateDirections(uv: self, gMap: self.gMapView, fromLocation: self.assignedDriverLocation, destinationLocation: self.destLocation, isCurrentLocationEnabled: false)
            self.updateDirection.onDirectionUpdateDelegate = self
            self.updateDirection.scheduleDirectionUpdate()
        }
        
        if(destLocation != nil){
            addWaitingMarker(fromLocation: self.pickUpLocation, toLocation: self.destLocation, waitingTime: "")
        }
        
        self.driverCancelTripView.isHidden = true
        
        setNavBar(isHidden: false)
        setLblToTitleView()
        self.titleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EN_ROUTE_TXT")
        
        self.emeImgView = UIImageView(frame: CGRect(x: 15,y: self.addressContainerView.frame.maxY + 10, width: 50, height: 50))
        self.emeImgView.image = UIImage(named: "ic_emergency")
        self.emeImgView.isUserInteractionEnabled = true
        
        self.view.addSubview(self.emeImgView)
        let emeTapGue = UITapGestureRecognizer()
        emeTapGue.addTarget(self, action: #selector(self.emeImgViewTapped))
        self.emeImgView.addGestureRecognizer(emeTapGue)
        
        if(deliveryConfirmCode != ""){
            let deliveryCodeTxt = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DELIVERY_CONFIRMATION_CODE_TXT") + ": " + deliveryConfirmCode
            var textHeight = deliveryCodeTxt.height(withConstrainedWidth: Application.screenSize.width - 20, font: UIFont(name: "Roboto-Light", size: 16)!) + 10
            
            if(textHeight > 50){
                textHeight = 50
            }
            
            self.deliveryConfirmCodeLbl.text = deliveryCodeTxt
            self.deliveryConfirmCodeLblHeight.constant = textHeight
            
            self.drvierDetailViewHeight = self.driverDetailView.frame.height + self.deliveryConfirmCodeLblHeight.constant
            
            self.deliveryConfirmCodeLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
            self.deliveryConfirmCodeLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
            
            //            self.driverDetailView.frame.origin.y = self.driverDetailView.frame.origin.y - self.deliveryConfirmCodeLblHeight.constant
            //            self.driverDetailView.frame.size = CGSize(width: self.driverDetailView.frame.width, height: self.driverDetailView.frame.height + self.deliveryConfirmCodeLblHeight.constant)
            
            
            //            self.driverDetailView.frame = CGRect(x: 0, y: self.driverDetailView.frame.origin.y - self.deliveryConfirmCodeLblHeight.constant, width: self.driverDetailView.frame.width, height: )
            //            Utils.printLog(msgData: "drvierDetailViewHeight::\(self.driverDetailView.frame.height)")
            //            self.myLocBottomMargin.constant = self.driverDetailView.frame.size.height + 15
            
        }
        
        self.setDriverDetailViewHeight()
        
    }
    
    func emeImgViewTapped(){
        let confirmEmergencyTapUV = GeneralFunctions.instantiateViewController(pageName: "ConfirmEmergencyTapUV") as! ConfirmEmergencyTapUV
        confirmEmergencyTapUV.iTripId = (self.assignedDriverData as NSDictionary).get("iTripId")
        self.pushToNavController(uv: confirmEmergencyTapUV)
    }
    
    func setUpDestination(){
        
        self.releaseAllTask()
        
        let window = Application.window
        
        let getUserData = GetUserData(uv: self, window: window!)
        getUserData.getdata()
        
        //        if(self.destLocation != nil){
        //            self.updateDirection = UpdateDirections(uv: self, gMap: self.gMapView, fromLocation: driverLocation, destinationLocation: self.destLocation, isCurrentLocationEnabled: false)
        //            self.updateDirection.scheduleDirectionUpdate()
        //        }
        
    }
    
    
    
    
    func requestCab(){
        
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            self.navigationController?.navigationBar.layer.zPosition = -1
        }
        
        
        
        if(userProfileJson.get("eEmailVerified").uppercased() != "YES" || userProfileJson.get("ePhoneVerified").uppercased() != "YES" ){
            
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                
                self.openAccountVerify()
                
            })
            
            return
        }
        
        currentLoadedDriverIdsArr.removeAll()
        
        addDriverNotificationObserver()
        
        requestCabView = self.generalFunc.loadView(nibName: "RequestCabView", uv: self, contentView: contentView)
        
        //        requestCabView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: self.view.frame.height)
        //        requestCabView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: Application.screenSize.height)
        requestCabView.frame = self.cntView.frame
        
        requestCabView.center = CGPoint(x: self.cntView.bounds.midX, y: self.cntView.bounds.midY)
        
        //        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
        //            requestCabHeaderViewHeightOffset = 20
        //        }
        
        //        if(Configurations.isIponeXDevice()){
        //            self.requestCabHeaderBarHeight.constant = 44 + GeneralFunctions.getSafeAreaInsets().top - requestCabHeaderViewHeightOffset
        //        }
        
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            requestCabView.frame.size.height = requestCabView.frame.size.height + getNavBarHeight()
            requestCabView.frame.origin.y = requestCabView.frame.origin.y - getNavBarHeight()
        }
        
        self.cntView.addSubview(requestCabView)
        
        requestCabTopView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        let cancelCabReqTapGue = UITapGestureRecognizer()
        cancelCabReqTapGue.addTarget(self, action: #selector(self.cancelCabRequest))
        
        cancelCabReqBgView.isUserInteractionEnabled = true
        cancelCabReqBgView.addGestureRecognizer(cancelCabReqTapGue)
        
        let titlePadding = getNavBarHeight() - 30
        self.requestCabTitleLbl.setPadding(paddingTop: titlePadding - 5, paddingBottom: 5, paddingLeft: 10, paddingRight: 10)
        
        //        if(!Configurations.isIponeXDevice()){
        //            self.requestCabTitleLbl.setPadding(paddingTop: self.requestCabTitleLbl.paddingTop, paddingBottom: self.requestCabTitleLbl.paddingBottom + 10, paddingLeft: self.requestCabTitleLbl.paddingLeft, paddingRight: self.requestCabTitleLbl.paddingRight)
        //        }
        
        self.requestCabTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_REQUESTING_TXT")
        //        self.requestCabTitleLbl.fitText()
        self.requestCabTitleLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        self.reqNoteLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NOTE_NO_DRIVER_REQUEST")
        
        
        self.retryReqBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RETRY_TXT"))
        self.retryReqBtn.clickDelegate = self
        
        self.reqNoteLbl.fitText()
        self.reqNoteLbl.textColor = UIColor.white
        
        
        requestCabView.backgroundColor = UIColor.black.withAlphaComponent(0.4)
        
        self.menuImgView.isHidden = true
        
        GeneralFunctions.setImgTintColor(imgView: self.cancelCabReqImgView, color: UIColor.UCAColor.AppThemeColor)
        self.cancelCabReqBgView.backgroundColor = UIColor.UCAColor.AppThemeTxtColor
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            
            if(self.requestCabView != nil){
                let halo = PulsingHaloLayer()
                halo.haloLayerNumber = 4
                halo.radius = 240.0
                halo.position = CGPoint(x: self.rippleBgView.frame.width / 2, y: self.rippleBgView.frame.height / 2)
                halo.backgroundColor = UIColor.UCAColor.AppThemeColor.cgColor
                self.rippleBgView.layer.addSublayer(halo)
                halo.start()
            }
        })
        
        //        self.rippleBgView.haloLayerNumber = 3
        //        self.rippleBgView.backgroundColor = UIColor.UCAColor.AppThemeColor.cgColor
        //        self.rippleBgView.start()
        
        self.currentLoadedDriverIdsArr = self.getAvailableDriverIds().components(separatedBy: ",")
        
        startDriverRequestQueue()
        
        
        if(self.loadAvailableCab != nil){
            self.loadAvailableCab.onPauseCalled()
        }
        
        
    }
    
    func getNavBarHeight() -> CGFloat{
        var navigationBarHeight: CGFloat = self.navigationController != nil ? (self.navigationController!.navigationBar.frame.height) : 64
        navigationBarHeight = navigationBarHeight + UIApplication.shared.statusBarFrame.height
        
        if(Configurations.isIponeXDevice() && self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            //            navigationBarHeight = 64
        }
        return navigationBarHeight
    }
    
    func getRetryTextHeight() -> CGFloat{
        
        var retryTxtHeight = self.reqNoteLbl.text!.height(withConstrainedWidth: Application.screenSize.width - 130, font: self.reqNoteLbl.font!)
        
        if(retryTxtHeight < 60){
            retryTxtHeight = 60
        }
        retryTxtHeight = retryTxtHeight + 10
        //        Utils.printLog(msgData: "retryTxtHeight::\(retryTxtHeight)")
        return retryTxtHeight
    }
    func incCountOfRequestToDriver(){
        if(userProfileJson.get("DRIVER_REQUEST_METHOD").uppercased() != "ALL"){
            continueDriverRequestQueue()
        }
    }
    
    
    func startDriverRequestQueue(){
        
        stopDriverRequestQueue()
        
        currDriverReqPosition = 0
        
        noDriverAvailView(isHidden: true)
        initializeDrverReqQue()
        
        driverRequestQueueTimer.fire()
        
    }
    
    func initializeDrverReqQue()
    {
        stopDriverRequestQueue()
        driverRequestQueueTimer =  Timer.scheduledTimer(timeInterval: Double(RIDER_REQUEST_ACCEPT_TIME + 5), target: self, selector: #selector(continueDriverRequestQueue), userInfo: nil, repeats: true)
    }
    
    func stopDriverRequestQueue(){
        if(driverRequestQueueTimer != nil){
            driverRequestQueueTimer!.invalidate()
            driverRequestQueueTimer = nil
        }
    }
    
    func continueDriverRequestQueue(){
        if(isRequestExecuting){
            return
        }
        
        if(currDriverReqPosition < currentLoadedDriverIdsArr.count){
            
            if(userProfileJson.get("DRIVER_REQUEST_METHOD").uppercased() == "ALL"){
                currDriverReqPosition = currentLoadedDriverIdsArr.count
                self.sendRequestToDrivers(driverIds: self.getAvailableDriverIds())
                
            }else{
                self.sendRequestToDrivers(driverIds: currentLoadedDriverIdsArr[currDriverReqPosition])
                currDriverReqPosition = currDriverReqPosition + 1
                
                initializeDrverReqQue()
            }
        }else{
            stopDriverRequestQueue()
            noDriverAvailView(isHidden: false)
        }
    }
    
    func noDriverAvailView(isHidden:Bool){
        if(requestCabView != nil){
            reqNoteLbl.isHidden = isHidden
            retryReqBtn.isHidden = isHidden
            
            if(isHidden == false){
                
                self.requestCabHeaderBarHeight.constant = getNavBarHeight() + getRetryTextHeight()
                
            }else{
                self.requestCabHeaderBarHeight.constant =  getNavBarHeight()
            }
        }
    }
    
    func closeCabReqView(){
        
        if(requestCabView != nil){
            stopDriverRequestQueue()
            self.requestCabView.removeFromSuperview()
            if(userProfileJson.get("APP_TYPE").uppercased() != "UBERX" && isDriverAssigned != true){
                self.menuImgView.isHidden = false
            }
            requestCabView = nil
        }
        
        self.isDeliveryDataEntered = false
    }
    
    
    func cancelCabRequest(){
        
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONFIRM_REQUEST_CANCEL_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Confirm", key: "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedIndex) in
            
            if(btnClickedIndex == 0){
                self.continueCancelCabReq()
            }
        })
        
    }
    
    func continueCancelCabReq(){
        let parameters = ["type":"cancelCabRequest", "iUserId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.releaseAllTask()
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                        self.releaseAllTask()
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                        return
                    }
                    
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func continueRideLaterSchedule(){
       
        
        let window = Application.window
        
        let parameters = ["type":"ScheduleARide", "iUserId": GeneralFunctions.getMemberd(),"pickUpLocAdd":self.pickUpAddress, "pickUpLatitude":"\(self.pickUpLocation!.coordinate.latitude)", "pickUpLongitude": "\(self.pickUpLocation!.coordinate.longitude)", "destLocAdd": "\(self.destAddress == "DEST_SKIPPED" ? "" : self.destAddress)","destLatitude":"\(self.destLocation == nil || self.destAddress == "DEST_SKIPPED" ? "" : "\(self.destLocation!.coordinate.latitude)")", "destLongitude": "\(self.destLocation == nil  || self.destAddress == "DEST_SKIPPED" ? "" : "\(self.destLocation!.coordinate.longitude)")", "scheduleDate": self.selectedDate, "iVehicleTypeId":selectedCabTypeId, "CashPayment":"\(isCashPayment)","eType":self.currentCabGeneralType,"PromoCode":self.appliedPromoCode, "iPackageTypeId": self.packageTypeId, "vReceiverName": self.receiverName, "vReceiverMobile": self.receiverMobile, "tPickUpIns": self.pickUpIns, "tDeliveryIns": self.deliveryIns, "tPackageDetails": self.packageDetails, "fTollPrice": "", "vTollPriceCurrencyCode": "", "eTollSkipped": "No", "Quantity": self.ufxSelectedQty, "SelectedDriverId": self.ufxSelectedServiceProviderId, "HandicapPrefEnabled": self.isHandicapPrefEnabled == true ? "Yes" : "No", "PreferFemaleDriverEnable": self.isPreferFemaleDriverEnable == true ? "Yes" : "No", "iUserAddressId": self.ufxAddressId, "tUserComment":self.specialInstruction, "iCabBookingId": ufxCabBookingId]
        
        //        "PickUpAddGeoCodeResult": self.pickUpAddGeoCodeResult.condenseWhitespace(), "DestAddGeoCodeResult": self.destAddGeoCodeResult.condenseWhitespace()
        //        , "TimeZone": self.selectedTimeZone
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") != "1"){
                    if(dataDict.get(Utils.message_str) == "SESSION_OUT"){
                        GeneralFunctions.logOutUser()
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                            
                            GeneralFunctions.restartApp(window: window!)
                        })
                        
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                        
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "NO_CARS" || dataDict.get(Utils.message_str) == "LBL_PICK_DROP_LOCATION_NOT_ALLOW" || dataDict.get(Utils.message_str) == "LBL_DROP_LOCATION_NOT_ALLOW" || dataDict.get(Utils.message_str) == "LBL_PICKUP_LOCATION_NOT_ALLOW"){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str) == "NO_CARS" ? "LBL_NO_CAR_AVAIL_TXT" : dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            self.closeCabReqView()
                            self.removeAddReqPickUpView()
                            
                        })
                        
                        return
                    }
                    
                    //                    if(dataDict.get(Utils.message_str) == "NO_CARS"){
                    //
                    //                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_CAR_AVAIL_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                    //
                    //                            self.closeCabReqView()
                    //                            self.removeAddReqPickUpView()
                    //
                    //                        })
                    //
                    //                        return
                    //                    }
                    
                    if(dataDict.get(Utils.message_str) == "DO_EMAIL_PHONE_VERIFY" || dataDict.get(Utils.message_str) == "DO_PHONE_VERIFY" || dataDict.get(Utils.message_str) == "DO_EMAIL_VERIFY"){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
                            accountVerificationUv.isMainScreen = true
                            accountVerificationUv.mainScreenUv = self
                            accountVerificationUv.requestType = dataDict.get(Utils.message_str)
                            self.pushToNavController(uv: accountVerificationUv)
                            
                        })
                    }
                    
                    
                }else{
                    self.loadBookingFinishView()
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
        
    }
    
    func loadBookingFinishView(){
        if(self.currentCabGeneralType == Utils.cabGeneralType_UberX){
            self.releaseAllTask()
            if(self.ufxCabBookingId != ""){
                self.performSegue(withIdentifier: "unwindToRideHistoryScreen", sender: self)
                return
            }
            self.performSegue(withIdentifier: "unwindToUFXHomeScreen", sender: self)
            return
        }
        let openBookingFinishedView = OpenBookingFinishedView(uv: self, containerView: self.contentView)
        openBookingFinishedView.currentInst = openBookingFinishedView
        openBookingFinishedView.show()
        
        if(self.requestPickUpView != nil){
            removeAddReqPickUpView()
        }
        
    }
    
    
    
    func sendRequestToDrivers(driverIds:String){
        isRequestExecuting = true
        var driverIds = driverIds
        if(self.currentCabGeneralType.uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            driverIds = self.ufxSelectedServiceProviderId
        }
        let window = Application.window
                
        let parameters = ["type":"sendRequestToDrivers", "driverIds": driverIds, "userId": GeneralFunctions.getMemberd(), "CashPayment": "\(isCashPayment)", "SelectedCarTypeID": selectedCabTypeId, "DestLatitude": "\(self.destLocation != nil && self.destAddress != "DEST_SKIPPED" ? "\(self.destLocation!.coordinate.latitude)" : "")", "DestLongitude": "\(self.destLocation != nil && self.destAddress != "DEST_SKIPPED" ? "\(self.destLocation!.coordinate.longitude)" : "")", "DestAddress": "\(self.destAddress == "DEST_SKIPPED" ? "" : self.destAddress)", "PickUpLatitude": "\(self.pickUpLocation!.coordinate.latitude)", "PickUpLongitude": "\(self.pickUpLocation!.coordinate.longitude)", "eType": self.currentCabGeneralType, "PromoCode": self.appliedPromoCode,"PickUpAddress": self.pickUpAddress, "iPackageTypeId": self.packageTypeId, "vReceiverName": self.receiverName, "vReceiverMobile": self.receiverMobile, "tPickUpIns": self.pickUpIns, "tDeliveryIns": self.deliveryIns, "tPackageDetails": self.packageDetails,  "Quantity": self.ufxSelectedQty, "iUserAddressId": self.ufxAddressId, "tUserComment":self.specialInstruction]
        
        //        "PickUpAddGeoCodeResult": self.pickUpAddGeoCodeResult.condenseWhitespace(), "DestAddGeoCodeResult": self.destAddGeoCodeResult.condenseWhitespace()
        //        , "TimeZone": "\(DateFormatter().timeZone.identifier)"
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            Utils.printLog(msgData: "response:\(response)")
            
            if(response != ""){
                self.isRequestExecuting = false
                
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") != "1"){
                    if(dataDict.get(Utils.message_str) == "SESSION_OUT"){
                        self.closeCabReqView()
                        self.stopDriverRequestQueue()
                        GeneralFunctions.logOutUser()
                        self.generalFunc.setAlertMessage(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SESSION_TIME_OUT"), content: self.generalFunc.getLanguageLabel(origValue: "Your session is expired. Please login again.", key: "LBL_SESSION_TIME_OUT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            GeneralFunctions.postNotificationSignal(key: Utils.releaseAllTaskObserverKey, obj: self)
                            
                            GeneralFunctions.restartApp(window: window!)
                        })
                        
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                        self.closeCabReqView()
                        self.stopDriverRequestQueue()
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                        
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "NO_CARS" && self.userProfileJson.get("DRIVER_REQUEST_METHOD").uppercased() != "ALL"){
                        self.incCountOfRequestToDriver()
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "NO_CARS" || dataDict.get(Utils.message_str) == "LBL_PICK_DROP_LOCATION_NOT_ALLOW" || dataDict.get(Utils.message_str) == "LBL_DROP_LOCATION_NOT_ALLOW" || dataDict.get(Utils.message_str) == "LBL_PICKUP_LOCATION_NOT_ALLOW"){
                        self.stopDriverRequestQueue()
                        self.closeCabReqView()
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str) == "NO_CARS" ? "LBL_NO_CAR_AVAIL_TXT" : dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            //                            self.removeAddReqPickUpView()
                            
                        })
                        
                        return
                    }
                    
                    if(dataDict.get(Utils.message_str) == "DO_EMAIL_PHONE_VERIFY" || dataDict.get(Utils.message_str) == "DO_PHONE_VERIFY" || dataDict.get(Utils.message_str) == "DO_EMAIL_VERIFY"){
                        self.stopDriverRequestQueue()
                        self.closeCabReqView()
                        
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Ok", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedIndex) in
                            
                            //                            self.removeAddReqPickUpView()
                            
                            let accountVerificationUv = GeneralFunctions.instantiateViewController(pageName: "AccountVerificationUV") as! AccountVerificationUV
                            accountVerificationUv.isMainScreen = true
                            accountVerificationUv.mainScreenUv = self
                            accountVerificationUv.requestType = dataDict.get(Utils.message_str)
                            self.pushToNavController(uv: accountVerificationUv)
                            
                        })
                    }
                    
                    
                }
                //                else{
                //                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                //                }
                
            }else{
                //                self.generalFunc.setError(uv: self)
                
                if(self.reqSentErrorDialog != nil){
                    self.reqSentErrorDialog.disappear()
                    self.reqSentErrorDialog = nil
                }
                self.reqSentErrorDialog = self.generalFunc.setAlertMessageWithReturnDialog(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: InternetConnection.isConnectedToNetwork() ? "LBL_TRY_AGAIN_TXT" : "LBL_NO_INTERNET_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "Retry", key: "LBL_RETRY_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedIndex) in
                    
                    if(btnClickedIndex == 0){
                        self.sendRequestToDrivers(driverIds: driverIds)
                    }else{
                        self.closeCabReqView()
                        self.stopDriverRequestQueue()
                        self.releaseAllTask()
                        
                        let window = Application.window
                        
                        let getUserData = GetUserData(uv: self, window: window!)
                        getUserData.getdata()
                    }
                })

                
//                if(self.userProfileJson.get("DRIVER_REQUEST_METHOD").uppercased() == "ALL"){
//                    self.currDriverReqPosition = 0
//                }else if(self.currDriverReqPosition > 0){
//                    self.currDriverReqPosition = self.currDriverReqPosition - 1
//                }
            }
        })
    }
    
    func openManageProfile(isOpenEditProfile: Bool){
        let manageProfileUv = GeneralFunctions.instantiateViewController(pageName: "ManageProfileUV") as! ManageProfileUV
        manageProfileUv.isOpenEditProfile = isOpenEditProfile
        //        (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(manageProfileUv, animated: true)
        self.pushToNavController(uv: manageProfileUv)
    }
    
    func setTripLocation(selectedAddress:String, selectedLocation:CLLocation){
        removeAnimLocations()
        if(self.isPickUpMode){
            if(self.pickUpLocation == nil){
                onAddressFound(address: selectedAddress, location: selectedLocation, isPickUpMode: self.isPickUpMode, dataResult: "")
                
                isSkipAddressFind = true
                addressContainerView.goToPickLoc()
                return
            }
            addressContainerView.pickUpAddressLbl.text = selectedAddress
            self.pickUpAddress = selectedAddress
            self.tempPickUpAdd = selectedAddress
            
            if(self.tempIsPickUpMode == false){
                addressContainerView.pickUpLocHLbl.text = selectedAddress
                self.tempIsPickUpMode  = self.isPickUpMode
            }
            
            self.pickUpLocation = selectedLocation
            if(self.loadAvailableCab != nil){
                self.loadAvailableCab.pickUpAddress = selectedAddress
                self.loadAvailableCab.setPickUpLocation(pickUpLocation: selectedLocation)
            }
            isSkipAddressFind = true
            //            addressContainerView.goToPickLoc()
            
            sourcePinImgView.image = UIImage(named: "ic_pin_source")
            
            sourcePinImgViewWidth.constant = 80
            sourcePinImgViewHeight.constant = 80
            sourcePinImgViewOffset.constant = -40
            
            if(self.loadAvailableCab != nil && requestCabView == nil){
                self.loadAvailableCab.onResumeCalled()
            }
        }else{
            
            if(self.isDriverAssigned == true){
                addDestinationOnTrip(latitude: "\(selectedLocation.coordinate.latitude)", longitude: "\(selectedLocation.coordinate.longitude)", address: selectedAddress, eConfirmByUser: "No")
                return
            }
            
            self.destLocation = selectedLocation
            self.destAddress = selectedAddress
            
            
            addressContainerView.destAddressLbl.text = selectedAddress == "DEST_SKIPPED" ? "" : selectedAddress
            isSkipAddressFind = true
            //            addressContainerView.goToDestLoc()
            
            sourcePinImgViewWidth.constant = 50
            sourcePinImgViewHeight.constant = 50
            sourcePinImgViewOffset.constant = -25
            
            sourcePinImgView.image = UIImage(named: "ic_pin_dest_selection")
            
            if(selectedAddress == "DEST_SKIPPED"){
                self.stopAnimatePolyLine()
            }
            
        }
        
        if(self.requestPickUpView != nil && self.pickUpLocation != nil && self.destLocation != nil){
            self.boundMapFromSourceToDest()
        }

        if(self.destLocation != nil && requestPickUpView == nil ){
            self.openRequestPickUpView(isFromRideLater: false)
        }
        
        if(requestPickUpView == nil){
            self.boundMapFromSourceToDest()
        }
        
        
        //        if(self.addressContainerView != nil){
        //            if(sourcePinImgView.isHidden == false){
        //                sourcePinImgView.image = UIImage(named: "ic_pin_dest_selection")
        //                self.addressContainerView.goToDestLoc()
        //            }
        //        }
        
        if(requestPickUpView != nil){
            self.cabTypesFareArr.removeAll()
            self.cabTypeCollectionView.reloadData()
            if(self.destAddress != "DEST_SKIPPED"){
                estimateFare()
            }else{
                self.addMarker()
                self.isRouteDrawnFailed = false
                
            }
        }
        
    }
    
    func continueLocationSelected(selectedLocation:CLLocation, selectedAddress:String, isFromAddDestination:Bool){
        if(self.isPickUpMode){
            addressContainerView.pickUpAddressLbl.text = selectedAddress
            self.pickUpAddress = selectedAddress
            self.pickUpLocation = selectedLocation
            if(self.loadAvailableCab != nil){
                self.loadAvailableCab.setPickUpLocation(pickUpLocation: selectedLocation)
            }
//            addressContainerView.goToPickLoc()
        }else{
            self.destLocation = selectedLocation
            self.destAddress = selectedAddress
            
            addressContainerView.destAddressLbl.text = selectedAddress
//            addressContainerView.goToDestLoc()
        }
        
        if(isFromAddDestination == false){
            self.getAllCenter()
        }else if(isFromAddDestination == true  && requestPickUpView != nil){
            self.boundMapFromSourceToDest()
        }
        
        
        if(self.destLocation != nil && requestPickUpView == nil){
            self.openRequestPickUpView(isFromRideLater: false)
        }
        
        if(requestPickUpView == nil){
            self.boundMapFromSourceToDest()
        }
        
        if(self.addressContainerView != nil){
            if(sourcePinImgView.isHidden == false){
                sourcePinImgView.image = UIImage(named: "ic_pin_dest_selection")
//                self.addressContainerView.goToDestLoc()
            }
        }
        
        if(requestPickUpView != nil){
            self.cabTypesFareArr.removeAll()
            self.cabTypeCollectionView.reloadData()
//            estimateFare()
            if(self.destAddress != "DEST_SKIPPED"){
                estimateFare()
            }
        }
        if(isFromAddDestination == false){
            self.getAllCenter()
        }
    }
    
    func addDestinationOnTrip(latitude: String, longitude: String, address:String, eConfirmByUser: String) {
        let parameters = ["type":"addDestination","iMemberId": GeneralFunctions.getMemberd(), "Latitude": latitude, "Longitude": longitude, "Address": address, "UserType": Utils.appUserType, "TripId": self.assignedTripId, "eConfirmByUser": eConfirmByUser]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    GeneralFunctions.saveValue(key: "IS_AUTO_FOCUS_TO_DEST", value: "Yes" as AnyObject)
                    
                    self.releaseAllTask()
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    if(dataDict.get("message").uppercased() == "YES"){
                        self.destinationOnTripLatitude = latitude
                        self.destinationOnTripLongitude = longitude
                        self.destinationOnTripAddress = address
                        
                        self.openSurgeConfirmDialog(isSurgeFromAddDestination: true, isRideLater: false, dataDict: dataDict)
                        return
                    }
                    
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    @IBAction func unwindToMainScreen(_ segue:UIStoryboardSegue) {
        //        unwindToSignUp
        
        if(segue.source.isKind(of: AddDestinationUV.self)){
            
            let addDestinationUv = segue.source as! AddDestinationUV
            let selectedLocation = addDestinationUv.selectedLocation
            let selectedAddress = addDestinationUv.selectedAddress
            
            if(addDestinationUv.isFromRecentLocView == true && self.recentLocView != nil){
                self.recentLocView.setSelectedLocations(latitude: selectedLocation!.coordinate.latitude, longitude: selectedLocation!.coordinate.longitude, address: selectedAddress, type: addDestinationUv.SCREEN_TYPE)
                //                return
            }
            GeneralFunctions.setSelectedLocations(latitude: selectedLocation!.coordinate.latitude, longitude: selectedLocation!.coordinate.longitude, address: selectedAddress, type: addDestinationUv.SCREEN_TYPE)
            
            if(isDriverAssigned == true){
                addDestinationOnTrip(latitude: "\(selectedLocation!.coordinate.latitude)", longitude: "\(selectedLocation!.coordinate.longitude)", address: selectedAddress, eConfirmByUser: "No")
            }else{
                
                if(self.pickUpLocation == nil){
                    onAddressFound(address: selectedAddress, location: selectedLocation!, isPickUpMode: self.isPickUpMode, dataResult: "")
                    
                    isSkipAddressFind = true
                    addressContainerView.goToPickLoc()
                    return
                }
                
                continueLocationSelected(selectedLocation: selectedLocation!, selectedAddress: selectedAddress, isFromAddDestination: true)
            }
            
            
        }else if(segue.source.isKind(of: AccountVerificationUV.self)){
            _ = segue.source as! AccountVerificationUV
            
        }else if(segue.source.isKind(of: AddPaymentUV.self)){
            if(self.requestPickUpView != nil){
                self.setCardMode()
            }
        }else if(segue.source.isKind(of: ChooseServiceDateUV.self)){
            let chooseServiceDateUV = segue.source as! ChooseServiceDateUV
            
            self.selectedDate = chooseServiceDateUV.finalDate
            
            if(self.loadAvailableCab != nil){
                self.loadAvailableCab.checkAvailableCabs()
            }
        }
        
    }
}
