//
//  HomeScreenContainerUV.swift
//  DriverApp
//
//  Created by NEW MAC on 27/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class HomeScreenContainerUV: UIViewController {
    
    @IBOutlet weak var containerView: UIView!
    
    var mainScreenUV:MainScreenUV!
    var driverStatesUV:DriverStatesUV!
    var accountSuspendUV:AccountSuspendUV!
    
    var isPageLoad = false
    
    
    let panRec = UIPanGestureRecognizer()
    let panRec_view = UIPanGestureRecognizer()
    
    override func viewWillAppear(_ animated: Bool) {
        if(Configurations.isRTLMode()){
           self.navigationDrawerController?.isRightPanGestureEnabled = true
        }else{
            self.navigationDrawerController?.isLeftPanGestureEnabled = true
        }
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        if(Configurations.isRTLMode()){
            self.navigationDrawerController?.isRightPanGestureEnabled = false
        }else{
            self.navigationDrawerController?.isLeftPanGestureEnabled = false
        }
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        GeneralFunctions.saveValue(key: "IS_DRIVER_ONLINE", value: "false" as AnyObject)
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let navHeight = self.navigationController!.navigationBar.frame.height
        let width = ((navHeight * 350) / 119)
        let imageView = UIImageView(frame: CGRect(x: 0, y: 0, width: width, height: ((width * 119) / 350)))
        imageView.contentMode = .scaleAspectFit
        
        let image = UIImage(named: "ic_your_logo")
        imageView.image = image
        
        
        let leftButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_menu_all")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openMenu))
        self.navigationItem.leftBarButtonItem = leftButton
        
        let rightButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_trans")!, style: UIBarButtonItemStyle.plain, target: self, action: nil)
        self.navigationItem.rightBarButtonItem = rightButton
        
        let eStatus = userProfileJson.get("eStatus")

        if(eStatus == "inactive"){
            self.navigationItem.titleView = imageView
            
            driverStatesUV = GeneralFunctions.instantiateViewController(pageName: "DriverStatesUV") as! DriverStatesUV
            driverStatesUV.view.frame = self.containerView.frame
            self.addChildViewController(driverStatesUV)
            self.addSubview(subView: driverStatesUV.view, toView: self.containerView)
            
        }else if(eStatus == "Suspend"){
            self.navigationItem.titleView = imageView
            
            accountSuspendUV = GeneralFunctions.instantiateViewController(pageName: "AccountSuspendUV") as! AccountSuspendUV
            accountSuspendUV.view.frame = self.containerView.frame
            self.addChildViewController(accountSuspendUV)
            self.addSubview(subView: accountSuspendUV.view, toView: self.containerView)
            
            let leftButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_trans")!, style: UIBarButtonItemStyle.plain, target: self, action: nil)
            self.navigationItem.leftBarButtonItem = leftButton
            
            
            let rightButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_nav_logout")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.logOutTapped))
            self.navigationItem.rightBarButtonItem = rightButton
            
            panRec.addTarget(self, action: #selector(self.panguster(sender:)))
            panRec_view.addTarget(self, action: #selector(self.panguster(sender:)))
            
            
            self.navigationController?.navigationBar.addGestureRecognizer(panRec)
            self.view.addGestureRecognizer(panRec_view)
            
        }else{
            
            mainScreenUV = GeneralFunctions.instantiateViewController(pageName: "MainScreenUV") as! MainScreenUV
            if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                self.navigationItem.titleView = imageView
            }else{
                
                let rightButton: UIBarButtonItem = UIBarButtonItem(image: UIImage(named: "ic_nav_refresh")!, style: UIBarButtonItemStyle.plain, target: mainScreenUV, action: #selector(mainScreenUV.onRefreshCalled))
                self.navigationItem.rightBarButtonItem = rightButton
            }
            
            mainScreenUV.view.frame = self.containerView.frame
            mainScreenUV.navItem = self.navigationItem
            self.addChildViewController(mainScreenUV)
            self.addSubview(subView: mainScreenUV.view, toView: self.containerView)
        
        }
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
    }
    
    deinit {
    }
    
    func releaseAllTask(){
        
        if(driverStatesUV != nil){
            driverStatesUV.releaseAllTask()
            driverStatesUV.view.removeFromSuperview()
            driverStatesUV.removeFromParentViewController()
            driverStatesUV.dismiss(animated: true, completion: nil)
            
            driverStatesUV = nil
            
            GeneralFunctions.removeObserver(obj: self)
            
            self.navigationDrawerController?.dismiss(animated: true, completion: nil)
        }
        
        if(mainScreenUV == nil){
            return
        }
        mainScreenUV.gMapView?.clear()
        mainScreenUV.gMapView?.stopRendering()
        mainScreenUV.gMapView?.removeFromSuperview()
        mainScreenUV.gMapView = nil
        
        mainScreenUV.releaseAllTask()
        mainScreenUV.view.removeFromSuperview()
        mainScreenUV.removeFromParentViewController()
        mainScreenUV.dismiss(animated: true, completion: nil)
        
        mainScreenUV = nil
        
        GeneralFunctions.removeObserver(obj: self)
        
        self.navigationDrawerController?.dismiss(animated: true, completion: nil)
    }
    
    func openMenu(){
        if(Configurations.isRTLMode()){
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: false)
            self.navigationDrawerController?.toggleRightView()
            
            //            self.navigationDrawerController?.setRightViewOpened(isRightViewOpened: true)
        }else{
            self.navigationDrawerController?.toggleLeftView()
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoad == false){
            mainScreenUV?.view.frame = self.containerView.frame
            driverStatesUV?.view.frame = self.containerView.frame
            
            isPageLoad = true
        }
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func addSubview(subView:UIView, toView parentView:UIView) {
        
        subView.frame = parentView.frame
        subView.center = CGPoint(x: parentView.bounds.midX, y: parentView.bounds.midY)
        
        parentView.addSubview(subView)
    }
    
    func logOutTapped(){
        let window = Application.window
        GeneralFunctions.logOutUser()
        GeneralFunctions.restartApp(window: window!)
    }
    
    func panguster(sender: UIPanGestureRecognizer) {
        
    }
}
