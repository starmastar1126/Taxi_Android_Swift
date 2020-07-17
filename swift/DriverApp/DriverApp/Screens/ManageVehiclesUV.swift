//
//  ManageVehiclesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 02/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ManageVehiclesUV: UIViewController, UITableViewDelegate, UITableViewDataSource, MyBtnClickDelegate {
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var noVehiclesView: UIView!
    @IBOutlet weak var noVehicleTitleLbl: MyLabel!
    @IBOutlet weak var addVehicleBtn: MyButton!
    @IBOutlet weak var noVehiclesViewHeight: NSLayoutConstraint!

    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var dataArrList = [NSDictionary]()
    var nextPage_str = 1
    var isLoadingMore = false
    var isNextPageAvail = false
    
    var cntView:UIView!
    
    var isSafeAreaSet = false
    var isDataSet = false
    
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "ManageVehiclesScreenDesign", uv: self, contentView: contentView)
        
        self.contentView.addSubview(cntView)
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ManageVehiclesScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
        
        self.tableView.delegate = self
        
        self.tableView.dataSource = self
//        self.tableView.contentInset = UIEdgeInsets(top: 6, left: 0, bottom: 6, right: 0)
        
        self.tableView.contentInset = UIEdgeInsetsMake(6, 0, GeneralFunctions.getSafeAreaInsets().bottom + 6, 0)
        
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "ManageVehiclesTVCell", bundle: nil), forCellReuseIdentifier: "ManageVehiclesTVCell")
        
        self.addVehicleBtn.clickDelegate = self
        self.addVehicleBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_VEHICLE"))
        self.noVehicleTitleLbl.text = self.generalFunc.getLanguageLabel(origValue: "You have not added ant vehicles. Please add your vehicle to continue your account process.", key: "LBL_ADD_VEHICLE_PAGE_GENERAL_NOTE")
        self.noVehicleTitleLbl.fitText()
//        let extraHeight = self.noVehicleTitleLbl.text.height(withConstrainedWidth: self.generalNoteLbl.frame.width, font: UIFont (name: "Roboto-Light", size: 18)!

        noVehiclesViewHeight.constant = 117.0 + (self.noVehicleTitleLbl.text!).height(withConstrainedWidth: Application.screenSize.width - 40, font: UIFont (name: noVehicleTitleLbl.font.fontName, size: noVehicleTitleLbl.font.pointSize)!) - 20

    }

    
    override func viewDidAppear(_ animated: Bool) {
        noVehiclesView.frame.size = CGSize(width: self.noVehiclesView.frame.width, height: self.addVehicleBtn.frame.maxY + 10)

//        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//            self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.view.frame.height)
//        })
//
        if(isDataSet == false){
            
            self.dataArrList.removeAll()
            getDtata()
            
            isDataSet = true
        }
        
        
    }
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.addVehicleBtn){
            openAddNewVehicle()
        }
    }
    
    func openAddNewVehicle(){
        let addVehiclesUv = GeneralFunctions.instantiateViewController(pageName: "AddVehiclesUV") as! AddVehiclesUV
        addVehiclesUv.manageVehiUV = self
        self.pushToNavController(uv: addVehiclesUv)
    }
    
    func getDtata(){
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
            loaderView.backgroundColor = UIColor.clear
        }else if(loaderView != nil && isLoadingMore == false){
            loaderView.isHidden = false
        }
        
        
        let parameters = ["type": "displaydrivervehicles", "MemberType": Utils.appUserType, "iMemberId": GeneralFunctions.getMemberd(), "page": self.nextPage_str.description]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                    }
                    let NextPage = dataDict.get("NextPage")
                    
                    if(NextPage != "" && NextPage != "0"){
                        self.isNextPageAvail = true
                        self.nextPage_str = Int(NextPage)!
                        
                        self.addFooterView()
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                    self.tableView.reloadData()
                    
                }else{
                    if(self.isLoadingMore == false){
//                        _ = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                }
                
                //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                
                
            }else{
                if(self.isLoadingMore == false){
                    self.generalFunc.setError(uv: self)
                    
                }
            }
            
            if(self.dataArrList.count > 0){
                self.noVehiclesView.isHidden = true
                self.addRightBarBtn()
            }else{
                self.noVehiclesView.isHidden = false
                self.navigationItem.rightBarButtonItem = nil
            }
            self.isLoadingMore = false
            self.loaderView.isHidden = true
            
        })
    }
    
    func addRightBarBtn(){
        let rightButton = UIBarButtonItem(image: UIImage(named: "ic_add_new_vehicle_nav_bar")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openAddNewVehicle))
        self.navigationItem.rightBarButtonItem = rightButton
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "ManageVehiclesTVCell", for: indexPath) as! ManageVehiclesTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.vOtherInfoLbl.text = item.get("vLicencePlate")
        
        if(item.get("eStatus") == "Active"){
            cell.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "Active", key: "LBL_ACTIVE")
        }else if(item.get("eStatus") == "Inactive"){
            cell.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "Inactive", key: "LBL_INACTIVE")
        }else if(item.get("eStatus") == "Deleted"){
            cell.statusLbl.text = self.generalFunc.getLanguageLabel(origValue: "Deleted", key: "LBL_DELETED")
        }else{
            cell.statusLbl.text = item.get("eStatus")
        }
        
        cell.vehicleNameLbl.text = item.get("vMake")
        
        cell.containerView.layer.shadowOpacity = 0.5
        cell.containerView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cell.containerView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        let padding = UIScreen.main.scale * 15
        
        
        let uploadDocImg = UIImage(named: "ic_upload_doc_list")!.imageWithInsets(insets: UIEdgeInsets(top: padding, left: padding, bottom: padding, right: padding))
        cell.uploadVehicleDocumentsImgView.image = uploadDocImg
        
        
        let editImg = UIImage(named: "ic_edit_vehicle")!.imageWithInsets(insets: UIEdgeInsets(top: padding, left: padding, bottom: padding, right: padding))
        cell.editVehicleImgView.image = editImg
        
//        UIEdgeInsets(top: 0, left: 0, bottom: 10, right: 0)
        let deleteImg = UIImage(named: "ic_delete")!.imageWithInsets(insets: UIEdgeInsets(top: padding, left: padding, bottom: padding, right: padding))
        cell.deleteVehicleImgView.image = deleteImg
        
        cell.uploadVehicleDocumentsImgView.tag = indexPath.item
        cell.editVehicleImgView.tag = indexPath.item
        cell.deleteVehicleImgView.tag = indexPath.item
        
        let editTapGue = UITapGestureRecognizer()
        let uploadDocTapGue = UITapGestureRecognizer()
        let deleteTapGue = UITapGestureRecognizer()
        
        editTapGue.addTarget(self, action: #selector(self.editVehicleImgTapped(sender:)))
        uploadDocTapGue.addTarget(self, action: #selector(self.uploadDocImgTapped(sender:)))
        deleteTapGue.addTarget(self, action: #selector(self.deleteImgTapped(sender:)))
        
        cell.uploadVehicleDocumentsImgView.isUserInteractionEnabled = true
        cell.editVehicleImgView.isUserInteractionEnabled = true
        cell.deleteVehicleImgView.isUserInteractionEnabled = true
        
        cell.uploadVehicleDocumentsImgView.addGestureRecognizer(uploadDocTapGue)
        cell.editVehicleImgView.addGestureRecognizer(editTapGue)
        cell.deleteVehicleImgView.addGestureRecognizer(deleteTapGue)
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        
        GeneralFunctions.setImgTintColor(imgView: cell.uploadVehicleDocumentsImgView, color: UIColor(hex: 0x201f1f))
        GeneralFunctions.setImgTintColor(imgView: cell.editVehicleImgView, color: UIColor(hex: 0x201f1f))
        GeneralFunctions.setImgTintColor(imgView: cell.deleteVehicleImgView, color: UIColor(hex: 0x201f1f))
        
        return cell
    }
    
    func editVehicleImgTapped(sender:UITapGestureRecognizer){
        let addVehiclesUv = GeneralFunctions.instantiateViewController(pageName: "AddVehiclesUV") as! AddVehiclesUV
        addVehiclesUv.currentVehicleData =  self.dataArrList[sender.view!.tag]
        addVehiclesUv.iDriverVehicleId = self.dataArrList[sender.view!.tag].get("iDriverVehicleId")
        addVehiclesUv.manageVehiUV = self
        self.pushToNavController(uv: addVehiclesUv)
    }
    
    func uploadDocImgTapped(sender:UITapGestureRecognizer){
    
        let listOfDocumentUV = GeneralFunctions.instantiateViewController(pageName: "ListOfDocumentUV") as! ListOfDocumentUV
        listOfDocumentUV.LIST_TYPE = "vehicle"
        listOfDocumentUV.iDriverVehicleId = self.dataArrList[sender.view!.tag].get("iDriverVehicleId")
        
        self.pushToNavController(uv: listOfDocumentUV)
    }
    
    func deleteImgTapped(sender:UITapGestureRecognizer){
        
        
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Do you want to delete this car?", key: "LBL_DELETE_CAR_SURE"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedId) in
            
            if(btnClickedId == 0){
                self.deleteCar(iDriverVehicleId: self.dataArrList[sender.view!.tag].get("iDriverVehicleId"))
            }
        })
    }
    
    func deleteCar(iDriverVehicleId:String){
    
        let parameters = ["type":"deletedrivervehicle","iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iDriverVehicleId": iDriverVehicleId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                        
                        self.dataArrList.removeAll()
                        self.tableView.reloadData()
                        self.getDtata()
                        
                    })
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            
        })
    }
    
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        let currentOffset = scrollView.contentOffset.y;
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
        
        
        if (maximumOffset - currentOffset <= 15) {
            
            if(isNextPageAvail==true && isLoadingMore==false){
                
                isLoadingMore=true
                
                getDtata()
            }
        }
    }
    
    func addFooterView(){
        let loaderView =  self.generalFunc.addMDloader(contentView: self.tableView, isAddToParent: false)
        loaderView.backgroundColor = UIColor.clear
        loaderView.frame = CGRect(x:0, y:0, width: Application.screenSize.width, height: 80)
        self.tableView.tableFooterView  = loaderView
        self.tableView.tableFooterView?.isHidden = false
    }
    
    func removeFooterView(){
        self.tableView.tableFooterView = UIView(frame: CGRect.zero)
        self.tableView.tableFooterView?.isHidden = true
    }

    
    @IBAction func unwindToManageVehicles(_ segue:UIStoryboardSegue) {
        
        if(segue.source.isKind(of: AddVehiclesUV.self) || segue.source.isKind(of: ListOfDocumentUV.self)){
            self.dataArrList.removeAll()
            self.tableView.reloadData()
            getDtata()
        }
    }
}
