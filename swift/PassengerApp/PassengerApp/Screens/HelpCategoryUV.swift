//
//  HelpCategoryUV.swift
//  PassengerApp
//
//  Created by iphone3 on 08/03/18.
//  Copyright © 2018 V3Cube. All rights reserved.
//

import UIKit

class HelpCategoryUV: UIViewController , UITableViewDataSource , UITableViewDelegate{
   
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    var loaderView:UIView!
    var dataArrList = [NSDictionary]()
    var categoryNameHeightContainer = [CGFloat]()
    var iTripId : String = ""
    var cntView:UIView!
    var isSafeAreaSet = false
    var isPageLoad = false
    
    override func viewDidLoad() {
        super.viewDidLoad()

        cntView = self.generalFunc.loadView(nibName: "HelpCategoryScreenDesign", uv: self, contentView: contentView)
        self.contentView.addSubview(cntView)
        
        loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        loaderView.backgroundColor = UIColor.clear
        
        self.tableView.isHidden = true
        self.tableView.dataSource = self
        self.tableView.delegate = self
        self.tableView.register(HelpCategoryListTVCell.self, forCellReuseIdentifier: "HelpCategoryListTVCell")
        self.tableView.register(UINib(nibName: "HelpCategoryListTVCell", bundle: nil), forCellReuseIdentifier: "HelpCategoryListTVCell")
        self.tableView.tableFooterView = UIView()
        self.tableView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 15, right: 0)
        
        addBackBarBtn()
        setData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
    }
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            self.cntView.frame.size.height = self.view.frame.height + GeneralFunctions.getSafeAreaInsets().bottom
            isSafeAreaSet = true
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoad == false){
            getHelpCategoryData()
            isPageLoad = true
        }
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HELP_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HELP_TXT")
    }
    
    func getHelpCategoryData(){
        self.categoryNameHeightContainer = []
        self.dataArrList.removeAll()
        
        let parameters = ["type":"getHelpDetailCategoty", "appType": Utils.appUserType, "iMemberId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        let categoryName = dataTemp.get("vTitle")
                        let categoryNameHeight = categoryName.height(withConstrainedWidth: Application.screenSize.width - 100, font: UIFont(name: "Roboto-Medium", size: 17)!)
                        self.categoryNameHeightContainer += [categoryNameHeight]
                        self.dataArrList += [dataTemp]
                    }
                    self.tableView.isHidden = false
                    self.tableView.reloadData()
                }else{
                    _ = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
            }else{
                self.generalFunc.setError(uv: self)
            }
            self.loaderView.isHidden = true
        })
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat{
            return self.categoryNameHeightContainer[indexPath.item] + 46
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "HelpCategoryListTVCell", for: indexPath) as! HelpCategoryListTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.categoryNameLbl.text = item.get("vTitle")
        cell.categoryNameLbl.removeGestureRecognizer(cell.categoryNameLbl.tapGue)
        cell.categoryNameLbl.font = UIFont(name: "Roboto-Medium", size: 17)!
        cell.categoryNameLbl.fitText()
        
        GeneralFunctions.setImgTintColor(imgView: cell.rightImgView, color: UIColor(hex: 0x9f9f9f))
        if(Configurations.isRTLMode()){
            cell.rightImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
        }
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let helpSubCategoryUV = GeneralFunctions.instantiateViewController(pageName: "HelpSubCategoryUV") as! HelpSubCategoryUV
        helpSubCategoryUV.selectedCategoryId = self.dataArrList[indexPath.item].get("iUniqueId")
        helpSubCategoryUV.iTripId = self.iTripId
        self.pushToNavController(uv: helpSubCategoryUV)
        
    }
}
