//
//  HelpUV.swift
//  DriverApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class HelpUV: UIViewController, UITableViewDelegate, UITableViewDataSource {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var dataArrList = [HelpCategoryItem]()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.contentView.addSubview(self.generalFunc.loadView(nibName: "HelpScreenDesign", uv: self, contentView: contentView))

        loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
        loaderView.backgroundColor = UIColor.clear
        self.tableView.dataSource = self
        self.tableView.delegate = self
        
        
        self.tableView.register(CountryListTVCell.self, forCellReuseIdentifier: "HelpCategoryListTVCell")
        self.tableView.register(UINib(nibName: "HelpCategoryListTVCell", bundle: nil), forCellReuseIdentifier: "HelpCategoryListTVCell")
        self.tableView.tableFooterView = UIView()
        
        self.addBackBarBtn()
        
        setData()
        
        getFAQData()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FAQ_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FAQ_TXT")
    }
    
    func getFAQData(){
        self.dataArrList.removeAll()
        
        let parameters = ["type":"getFAQ", "appType": Utils.appUserType, "iMemberId": GeneralFunctions.getMemberd()]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        let helpCatItem = HelpCategoryItem(vTitle: dataTemp.get("vTitle"), questionList: dataTemp.getArrObj("Questions"))
                        
                        self.dataArrList += [helpCatItem]
                        
                    }
                    
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
        
        cell.categoryNameLbl.text = item.vTitle
        cell.categoryNameLbl.removeGestureRecognizer(cell.categoryNameLbl.tapGue)
        
        GeneralFunctions.setImgTintColor(imgView: cell.rightImgView, color: UIColor(hex: 0x9f9f9f))
        if(Configurations.isRTLMode()){
            cell.rightImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
        }
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let selectedHelpCategoryItem = self.dataArrList[indexPath.item]
        
        let helpQuestionAnswersUv = GeneralFunctions.instantiateViewController(pageName: "HelpQuestionAnswersUV") as! HelpQuestionAnswersUV
        
        helpQuestionAnswersUv.selectedHelpCategoryItem = selectedHelpCategoryItem
        
        self.pushToNavController(uv: helpQuestionAnswersUv)
        
    }
}

class HelpCategoryItem {
    
    var vTitle:String!
    var questionList:NSArray!
    
    // MARK: Initialization
    
    init(vTitle: String, questionList:NSArray) {
        // Initialize stored properties.
        self.vTitle = vTitle
        self.questionList = questionList
        
    }
}
