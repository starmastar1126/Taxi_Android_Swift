 //
//  RecentLocationView.swift
//  PassengerApp
//
//  Created by NEW MAC on 21/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RecentLocationView: UIView, MyLabelClickDelegate, UITableViewDelegate, UITableViewDataSource {
    
    typealias ViewStateHandler = (_ isViewElapsed:Bool, _ isViewCollapsed:Bool,_ isViewDraging:Bool, _ isViewDragingEnd:Bool) -> Void
    typealias OnLocationSelectedHandler = (_ latitude:Double, _ longitude:Double,_ address:String) -> Void

    @IBOutlet weak var placesHLbl: MyLabel!
    @IBOutlet weak var homeLocAreaView: UIView!
    @IBOutlet weak var homeLocHLbl: MyLabel!
    @IBOutlet weak var homeLocVLbl: MyLabel!
    @IBOutlet weak var homeLocEditImgView: UIImageView!
    @IBOutlet weak var placeHlblWidth: NSLayoutConstraint!
    @IBOutlet weak var homeLocImgView: UIImageView!
    @IBOutlet weak var workLocImgView: UIImageView!
    
    
    @IBOutlet weak var workLocAreaView: UIView!
    @IBOutlet weak var workLocHLbl: MyLabel!
    @IBOutlet weak var workLocVLbl: MyLabel!
    @IBOutlet weak var workLocEditImgView: UIImageView!
    
    @IBOutlet weak var recentLocationHLbl: MyLabel!
    @IBOutlet weak var recentLocTableView: UITableView!
    
    var mainScreenUV:MainScreenUV!
    
    var view: UIView!
    
    var isViewHidden = false
    
    let generalFunc = GeneralFunctions()
    var handler:ViewStateHandler!
    var locSelectHandler:OnLocationSelectedHandler!
    let dragView = SDragView()
    
    var dataArrList = [RecentLocationItem]()
    
    override init(frame: CGRect) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    func setStateHandler(handler: @escaping ViewStateHandler){
        self.handler = handler
    }
    
    func setLocationSelectHandler(locSelectHandler:  @escaping OnLocationSelectedHandler){
        self.locSelectHandler = locSelectHandler
    }
    
    func xibSetup() {
        view = loadViewFromNib()
        view.frame = bounds
        
        
        self.autoresizingMask = [.flexibleHeight,.flexibleLeftMargin,.flexibleRightMargin]
//        view.setViewHandler { (dragView, boundsRect) in
//            
//            self.view.frame = boundsRect
//        }
        
//        view.setHandler { (isViewElapsed, isViewCollapsed, isViewDraging, isViewDragingEnd) in
//            if(self.handler != nil){
//                self.handler(isViewElapsed,isViewCollapsed,isViewDraging,isViewDragingEnd)
//            }
//        }
        
        self.isUserInteractionEnabled = true
        // Make the view stretch with containing view
        //        view.autoresizingMask = [UIViewAutoresizing.flexibleWidth, UIViewAutoresizing.flexibleHeight]
        // Adding custom subview on top of our view (over any custom drawing > see note below)
        addSubview(view)
        
        if(isViewHidden == true){
            view.isHidden = isViewHidden
            view.removeFromSuperview()
        }
        
        self.placesHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
        self.placesHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        
        self.recentLocationHLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
        self.recentLocationHLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        self.recentLocationHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Recent Locations", key: "LBL_RECENT_LOCATIONS")
        self.placesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Recent and Favorite Places", key: "LBL_PLACE_RECENT_FAV")
        
        self.placesHLbl.setClickDelegate(clickDelegate: self)
        checkPlaces()
        
        
        self.recentLocTableView.dataSource = self
        self.recentLocTableView.delegate = self
        
        
//        self.recentLocTableView.register(RecentLocationView.self, forCellReuseIdentifier: "RecentLocationTVCell")
        self.recentLocTableView.register(UINib(nibName: "RecentLocationTVCell", bundle: nil), forCellReuseIdentifier: "RecentLocationTVCell")
        self.recentLocTableView.tableFooterView = UIView()
        
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.placesHLbl){
            dragView.buttonAction(sender: UIButton())
        }else if(sender == self.homeLocHLbl || sender == self.homeLocVLbl){
            homePlaceTapped()
        }else if(sender == self.workLocHLbl || sender == self.workLocVLbl){
            workPlaceTapped()
        }
    }
    
    
    func getHomePlaceTapGue() -> UITapGestureRecognizer{
        let homePlaceTapGue = UITapGestureRecognizer()
        homePlaceTapGue.addTarget(self, action: #selector(self.homePlaceTapped))
        
        return homePlaceTapGue
    }
    
    func getWorkPlaceTapGue() -> UITapGestureRecognizer{
        let workPlaceTapGue = UITapGestureRecognizer()
        workPlaceTapGue.addTarget(self, action: #selector(self.workPlaceTapped))
        
        return workPlaceTapGue
    }
    
    func homePlaceTapped(){
        dragView.buttonAction(sender: UIButton())
        self.mainScreenUV.continueLocationSelected(selectedLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLatitude") as! String), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userHomeLocationLongitude") as! String)), selectedAddress: (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String), isFromAddDestination: false)
    }
    
    func workPlaceTapped(){
        dragView.buttonAction(sender: UIButton())
        self.mainScreenUV.continueLocationSelected(selectedLocation: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLatitude") as! String), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: GeneralFunctions.getValue(key: "userWorkLocationLongitude") as! String)), selectedAddress: (GeneralFunctions.getValue(key: "userWorkLocationAddress") as! String), isFromAddDestination: false)
    }
    
    func checkRecentPlaces(){
        
        if(mainScreenUV == nil){
            return
        }
        
        self.dataArrList.removeAll()
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let sourceLocations = userProfileJson.getArrObj("SourceLocations")
        let destLocations = userProfileJson.getArrObj("DestinationLocations")
        
        if(self.mainScreenUV.isPickUpMode == true){
            for i in 0..<sourceLocations.count{
                let currentItem = sourceLocations[i] as! NSDictionary
                
                let recentLocItem = RecentLocationItem(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tStartLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tStartLong"))), address: currentItem.get("tSaddress"))
                
                self.dataArrList += [recentLocItem]
            }
        }else{
            for i in 0..<destLocations.count{
                let currentItem = destLocations[i] as! NSDictionary
                
                let recentLocItem = RecentLocationItem(location: CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tEndLat")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: currentItem.get("tEndLong"))), address: currentItem.get("tDaddress"))
                
                self.dataArrList += [recentLocItem]
            }
        }
        
        if(self.dataArrList.count < 1){
            self.recentLocTableView.isHidden = true
            self.recentLocationHLbl.isHidden = true
        }else{
            self.recentLocTableView.isHidden = false
            self.recentLocationHLbl.isHidden = false
        }
        
        self.recentLocTableView.reloadData()
    
    }
    
    func checkPlaces(){
        
        checkRecentPlaces()
        
        let userHomeLocationAddress = GeneralFunctions.getValue(key: "userHomeLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userHomeLocationAddress") as! String) : ""
        let userWorkLocationAddress = GeneralFunctions.getValue(key: "userWorkLocationAddress") != nil ? (GeneralFunctions.getValue(key: "userWorkLocationAddress") as! String) : ""
        
        if(userHomeLocationAddress != ""){
            self.homeLocEditImgView.image = UIImage(named: "ic_edit")
            self.homeLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOME_PLACE")
            self.homeLocVLbl.text = GeneralFunctions.getValue(key: "userHomeLocationAddress") as? String
            
            self.homeLocHLbl.setClickDelegate(clickDelegate: self)
            self.homeLocVLbl.setClickDelegate(clickDelegate: self)
            
            self.homeLocImgView.isUserInteractionEnabled = true
            self.homeLocImgView.addGestureRecognizer(self.getHomePlaceTapGue())
        }else{
            self.homeLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.homeLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_HOME_PLACE_TXT")
            self.homeLocHLbl.text = "----"
        }
        
        if(userWorkLocationAddress != ""){
            self.workLocEditImgView.image = UIImage(named: "ic_edit")
            self.workLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WORK_PLACE")
            self.workLocVLbl.text = GeneralFunctions.getValue(key: "userWorkLocationAddress") as? String
            
            
            self.workLocHLbl.setClickDelegate(clickDelegate: self)
            self.workLocVLbl.setClickDelegate(clickDelegate: self)
            
            self.workLocImgView.isUserInteractionEnabled = true
            self.workLocImgView.addGestureRecognizer(self.getWorkPlaceTapGue())
        }else{
            self.workLocEditImgView.image = UIImage(named: "ic_add_plus")
            self.workLocVLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_WORK_PLACE_TXT")
            self.workLocHLbl.text = "----"
        }
        
        GeneralFunctions.setImgTintColor(imgView: self.homeLocImgView, color: UIColor(hex: 0x353e45))
        GeneralFunctions.setImgTintColor(imgView: self.workLocImgView, color: UIColor(hex: 0x353e45))
        GeneralFunctions.setImgTintColor(imgView: self.homeLocEditImgView, color: UIColor(hex: 0x858f93))
        GeneralFunctions.setImgTintColor(imgView: self.workLocEditImgView, color: UIColor(hex: 0x858f93))
        
        let homePlaceTapGue = UITapGestureRecognizer()
        let workPlaceTapGue = UITapGestureRecognizer()
        
        homePlaceTapGue.addTarget(self, action: #selector(self.homePlaceEditTapped))
        workPlaceTapGue.addTarget(self, action: #selector(self.workPlaceEditTapped))
        
        self.homeLocEditImgView.isUserInteractionEnabled = true
        self.homeLocEditImgView.addGestureRecognizer(homePlaceTapGue)
        
        self.workLocEditImgView.isUserInteractionEnabled = true
        self.workLocEditImgView.addGestureRecognizer(workPlaceTapGue)
    }
    
    func homePlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "HOME"
        addDestinationUv.isFromRecentLocView = true
        self.mainScreenUV.pushToNavController(uv: addDestinationUv)
    }
    
    func workPlaceEditTapped(){
        let addDestinationUv = GeneralFunctions.instantiateViewController(pageName: "AddDestinationUV") as! AddDestinationUV
        addDestinationUv.SCREEN_TYPE = "WORK"
        addDestinationUv.isFromRecentLocView = true
        self.mainScreenUV.pushToNavController(uv: addDestinationUv)
    }
    
    func setSelectedLocations(latitude:Double, longitude:Double, address:String, type:String){
        if(type == "HOME"){
            GeneralFunctions.saveValue(key: "userHomeLocationAddress", value: address as AnyObject)
            GeneralFunctions.saveValue(key: "userHomeLocationLatitude", value: ("\(latitude)") as AnyObject)
            GeneralFunctions.saveValue(key: "userHomeLocationLongitude", value: ("\(longitude)") as AnyObject)
        }else if(type == "WORK"){
            GeneralFunctions.saveValue(key: "userWorkLocationAddress", value: address as AnyObject)
            GeneralFunctions.saveValue(key: "userWorkLocationLatitude", value: ("\(latitude)") as AnyObject)
            GeneralFunctions.saveValue(key: "userWorkLocationLongitude", value: ("\(longitude)") as AnyObject)
        }
        
        dragView.buttonAction(sender: UIButton())
        
        checkPlaces()
        
    }
    
    func initializeDragView(dragViewAnimatedTopSpace:CGFloat, viewDefaultHeightConstant:CGFloat, containerView: UIView){
    
        dragView.setViewHandler { (dragView, boundsRect) in
            
            self.placeHlblWidth.constant = boundsRect.size.width
//            self.frame = boundsRect
//            self.view.frame = boundsRect
//            self.view.autoresizingMask = [.flexibleWidth, .flexibleHeight]
//            self.view.frame = boundsRect
        }

        dragView.initializeDragView(view: self, dragViewAnimatedTopSpace: dragViewAnimatedTopSpace, viewDefaultHeightConstant: viewDefaultHeightConstant, containerView: containerView)
        
        dragView.setHandler { (isViewElapsed, isViewCollapsed, isViewDraging, isViewDragingEnd, boundsRect) in
            
//            if(isViewDragingEnd == true){
//               self.placeHlblWidth.constant = boundsRect.size.width
//                
//            }
            
            if(isViewElapsed == true){
                self.placesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Favorite Locations", key: "LBL_FAV_LOCATIONS")
            }else{
                self.placesHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Recent and Favorite Places", key: "LBL_PLACE_RECENT_FAV")
            }
            
            if(self.handler != nil){
                self.handler(isViewElapsed,isViewCollapsed,isViewDraging,isViewDragingEnd)
            }
        }
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "RecentLocationTVCell", for: indexPath) as! RecentLocationTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.recentAddressLbl.text = item.address
       
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let item = self.dataArrList[indexPath.item]
        
        dragView.buttonAction(sender: UIButton())
        self.mainScreenUV.continueLocationSelected(selectedLocation: item.location, selectedAddress: item.address, isFromAddDestination: false)
    }
    
    
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "RecentLocationView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }

}
 
 class RecentLocationItem {
    
    var location:CLLocation!
    var address:String!
    
    // MARK: Initialization
    
    init(location: CLLocation, address:String) {
        // Initialize stored properties.
        self.location = location
        self.address = address
        
    }
 }
