//
//  HeatViewUV.swift
//  DriverApp
//
//  Created by NEW MAC on 03/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps
import CoreLocation

class HeatViewUV: UIViewController, GMSMapViewDelegate, OnLocationUpdateDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var gMapContainerView: UIView!
    @IBOutlet weak var myLocBtnImgView: UIImageView!
    
    var isDataSet = false
    
    var historyData = [String]()
    var onlineData = [String]()
    
    var currentRadius = 0.0
    var dtaCircleHeatMap = [GMSMarker]()
    
    var zoomLevel:Float  = 4
    var isFirstLocationUpdate = true
    
    let generalFunc = GeneralFunctions()
    
    var locationDialog:OpenLocEnaPassView!
    
    var getLocation:GetLocation!
    var window:UIWindow!
    
    var task_update_heatMapData: ExeServerUrl?
    
    var currentLocation:CLLocation!
    
    var gMapView:GMSMapView!
    
    private var gradientColors = [UIColor.red , UIColor.white]
    private var gradientStartPoints = [0.005,1.0]
    private var heatmapLayer: GMUHeatmapTileLayer!
    private var onlineHeatmapLayer: GMUHeatmapTileLayer!
    private var onlineGradientColors = [UIColor.green]
    private var onlineGradientStartPoints = [0.2]
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "HeatViewScreenDesign", uv: self, contentView: contentView))
        
        window = Application.window!
        
        myLocBtnImgView.isUserInteractionEnabled = true
        
        let myLocTapGue = UITapGestureRecognizer()
        myLocTapGue.addTarget(self, action: #selector(self.myLocImgTapped))
        
        myLocBtnImgView.addGestureRecognizer(myLocTapGue)
        
        self.addBackBarBtn()
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isDataSet == false){
            let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: Utils.defaultZoomLevel)
            gMapView = GMSMapView.map(withFrame: self.gMapContainerView.frame, camera: camera)
            //        googleMapContainerView = gMapView
            //        gMapView = GMSMapView()
            gMapView.isMyLocationEnabled = true
            self.gMapContainerView.addSubview(gMapView)
            
            setData()
            
            isDataSet = true
        }
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInBackground), name: NSNotification.Name(rawValue: Utils.appBGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        
        checkLocationEnabled()
    }
    override func viewWillDisappear(_ animated: Bool) {
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: Utils.appBGNotificationKey), object: nil)
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func myLocImgTapped(){
        if(currentLocation == nil){
            return
        }
        let camera = GMSCameraPosition.camera(withLatitude: currentLocation.coordinate.latitude,
                                              longitude: currentLocation.coordinate.longitude, zoom: Utils.defaultZoomLevel)

        self.gMapView.animate(to: camera)
    }
    
    func appInBackground(){
    }
    
    func appInForground(){
        checkLocationEnabled()
    }
    
    func checkLocationEnabled(){
        if(locationDialog != nil){
            locationDialog.closeView()
            locationDialog = nil
        }
        
        if((GeneralFunctions.hasLocationEnabled() == false && self.currentLocation == nil) || InternetConnection.isConnectedToNetwork() == false)
        {
            
            locationDialog = OpenLocEnaPassView(uv: self, containerView: self.contentView, menuImgView: UIImageView())
            locationDialog.currentInst = locationDialog
            locationDialog.setViewHandler(handler: { (latitude, longitude, address, isMenuOpen) in
                
                self.onLocationUpdate(location: CLLocation(latitude: latitude, longitude: longitude))
                
            })
            
            locationDialog.show()
            
            return
        }
    }
    func openMenu(){
        
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.isRightPanGestureEnabled = true
            self.navigationDrawerController?.toggleRightView()
            
        }else{
            self.navigationDrawerController?.isLeftPanGestureEnabled = true
            self.navigationDrawerController?.toggleLeftView()
        }
    }
    
    override func closeCurrentScreen() {
        
        releaseAllTask()
        
        super.closeCurrentScreen()
    }
    
    deinit {
        releaseAllTask()
        
    }
    
    func releaseAllTask(){
        
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
    }
    
    func setData(){
        
        onlineData.removeAll()
        historyData.removeAll()
        dtaCircleHeatMap.removeAll()
        currentRadius = 0
        
        gMapView.clear()
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MENU_MY_HEATVIEW")
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MENU_MY_HEATVIEW")
    }
    
    func onLocationUpdate(location: CLLocation) {
        
        if(gMapView == nil){
            releaseAllTask()
            return
        }
        checkLocationEnabled()
        self.currentLocation = location
        
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
            
        }
//        let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
//                                              longitude: location.coordinate.longitude, zoom: currentZoomLevel)
        
        
        if(self.isFirstLocationUpdate == true){
            
            let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude, longitude: location.coordinate.longitude, zoom: Utils.defaultZoomLevel)

            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
            
            loadHeatMapData()
        }
        else{
//            self.gMapView.animate(to: camera)
        }
        
        isFirstLocationUpdate = false
        
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
    
    func mapView(_ mapView: GMSMapView, idleAt position: GMSCameraPosition) {
        loadHeatMapData()
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
            
            if(self.gMapView.delegate == nil){
                self.gMapView.delegate = self
            }
            
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
        
        var list = [GMUWeightedLatLng]()
        var onlineList = [GMUWeightedLatLng]()
        if(dict.get("Action")  == "1"){
            self.currentRadius = radius
            let message = dict.get("message")
            
            if(message != ""){
                let message_arr = dict.getArrObj("message")
                
                for i in 0 ..< message_arr.count {
                    let tempItem = message_arr[i] as! NSDictionary
                    
                    let type = tempItem.get("Type")
                    
                    
                    let latitude = Double(tempItem.get("Latitude"))
                    let longitude = Double(tempItem.get("Longitude"))
                    
                    let coords = GMUWeightedLatLng(coordinate: CLLocationCoordinate2DMake(latitude!, longitude!), intensity: 1.0)
                    
//                    let loc = CLLocation(latitude: latitude.doubleValue, longitude: longitude.doubleValue)
//                    
//                    let circle = GMSMarker()
//                    
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
//                heatmapLayer.radius = 20
                
                self.heatmapLayer.weightedData = list
                heatmapLayer.map = gMapView
                
                onlineHeatmapLayer = GMUHeatmapTileLayer()
                onlineHeatmapLayer.gradient = GMUGradient(colors: onlineGradientColors,
                                                    startPoints: onlineGradientStartPoints as [NSNumber],
                                                    colorMapSize: 256)
//                onlineHeatmapLayer.opacity = 1
//                onlineHeatmapLayer.radius = 20
                
                self.onlineHeatmapLayer.weightedData = onlineList
                onlineHeatmapLayer.map = gMapView
                
                self.gMapView.setNeedsDisplay()
            }
        }
    }
    
    
    
    @IBAction func plusBtnTapped(_ sender: UIButton) {
        self.gMapView.animate(toZoom: self.gMapView.camera.zoom + 1)
    }
    
    @IBAction func minusBtnTapped(_ sender: UIButton) {
        self.gMapView.animate(toZoom: self.gMapView.camera.zoom - 1)
    }
    
}
